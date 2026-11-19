<?php
namespace Tests\Feature;
use App\Models\Estudiante;
use Database\Seeders\EstudianteSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class EstudianteCrudTest extends TestCase
{
    use RefreshDatabase;
    private function datos(array $cambios = []): array {
        return array_merge(['codigo'=>'2026100099','nombres'=>'Ana','apellidos'=>'Torres',
            'email'=>'ana@example.com','ciclo'=>'VI','promedio'=>'16.50'], $cambios);
    }
    public function test_raiz_redirige_al_modulo(): void { $this->get('/')->assertRedirect('/estudiantes'); }
    public function test_listado_vacio_y_formulario(): void {
        $this->get('/estudiantes')->assertOk()->assertSee('Aún no hay estudiantes');
        $this->get('/estudiantes/create')->assertOk()->assertSee('Guardar estudiante');
    }
    public function test_registro_valido_guarda_y_confirma(): void {
        $this->post('/estudiantes',$this->datos())->assertRedirect(route('estudiantes.index'))->assertSessionHas('success');
        $this->assertDatabaseHas('estudiantes',['codigo'=>'2026100099','promedio'=>16.50]);
    }
    public function test_codigo_y_email_duplicados_se_rechazan(): void {
        Estudiante::create($this->datos());
        $this->post('/estudiantes',$this->datos())->assertSessionHasErrors(['codigo','email']);
        $this->assertDatabaseCount('estudiantes',1);
    }
    public function test_campos_obligatorios(): void {
        $this->post('/estudiantes',[])->assertSessionHasErrors(['codigo','nombres','apellidos','email','ciclo','promedio']);
        $this->assertDatabaseCount('estudiantes',0);
    }
    public function test_promedio_invalido_conserva_datos(): void {
        $this->from('/estudiantes/create')->post('/estudiantes',$this->datos(['promedio'=>25]))
            ->assertRedirect('/estudiantes/create')->assertSessionHasErrors('promedio')->assertSessionHasInput('nombres','Ana');
        $this->assertDatabaseCount('estudiantes',0);
    }
    public function test_limites_cero_y_veinte_validos(): void {
        $this->post('/estudiantes',$this->datos(['promedio'=>0]))->assertSessionHasNoErrors();
        $this->post('/estudiantes',$this->datos(['codigo'=>'2026100098','email'=>'otra@example.com','promedio'=>20]))->assertSessionHasNoErrors();
        $this->assertDatabaseCount('estudiantes',2);
    }
    public function test_codigo_email_ciclo_y_precision_invalidos(): void {
        $this->post('/estudiantes',$this->datos(['codigo'=>'ABCDEFGHIJ','email'=>'correo-invalido','ciclo'=>'XI','promedio'=>'16.555']))
            ->assertSessionHasErrors(['codigo','email','ciclo','promedio']);
    }
    public function test_longitudes_y_promedio_negativo(): void {
        $this->post('/estudiantes',$this->datos(['nombres'=>str_repeat('a',61),'apellidos'=>str_repeat('b',61),'promedio'=>-1]))
            ->assertSessionHasErrors(['nombres','apellidos','promedio']);
    }
    public function test_paginacion_de_cinco_registros(): void {
        $this->seed(EstudianteSeeder::class);
        $this->get('/estudiantes')->assertOk()->assertViewHas('estudiantes',fn($p)=>$p->count()===5 && $p->total()===8);
        $this->get('/estudiantes?page=2')->assertOk()->assertViewHas('estudiantes',fn($p)=>$p->count()===3);
    }
    public function test_ficha_y_edicion_cargan_datos(): void {
        $e=Estudiante::create($this->datos());
        $this->get('/estudiantes/'.$e->id)->assertOk()->assertSee('ana@example.com');
        $this->get('/estudiantes/'.$e->id.'/edit')->assertOk()->assertSee('2026100099');
    }
    public function test_actualizar_sin_cambiar_identificadores(): void {
        $e=Estudiante::create($this->datos());
        $this->travel(2)->seconds();
        $this->put('/estudiantes/'.$e->id,$this->datos(['ciclo'=>'VII','promedio'=>'18.25']))->assertSessionHasNoErrors()->assertRedirect(route('estudiantes.index'));
        $e->refresh();
        $this->assertSame('18.25',$e->promedio);
        $this->assertSame('VII',$e->ciclo);
        $this->assertTrue($e->updated_at->greaterThan($e->created_at));
    }
    public function test_actualizar_con_datos_de_otro_estudiante_se_rechaza(): void {
        $e=Estudiante::create($this->datos());
        Estudiante::create($this->datos(['codigo'=>'2026100098','email'=>'otro@example.com']));
        $this->put('/estudiantes/'.$e->id,$this->datos(['codigo'=>'2026100098','email'=>'otro@example.com']))->assertSessionHasErrors(['codigo','email']);
        $this->assertSame('2026100099',$e->fresh()->codigo);
    }
    public function test_eliminar_registro(): void {
        $e=Estudiante::create($this->datos());
        $this->delete('/estudiantes/'.$e->id)->assertRedirect(route('estudiantes.index'))->assertSessionHas('success');
        $this->assertDatabaseMissing('estudiantes',['id'=>$e->id]);
    }
    public function test_id_inexistente_retorna_404(): void {
        $this->get('/estudiantes/999999')->assertNotFound();
        $this->get('/estudiantes/999999/edit')->assertNotFound();
        $this->put('/estudiantes/999999',$this->datos())->assertNotFound();
        $this->delete('/estudiantes/999999')->assertNotFound();
    }
    public function test_asignacion_masiva_no_acepta_id_ni_fechas(): void {
        $this->post('/estudiantes',$this->datos(['id'=>99999,'created_at'=>'2000-01-01 00:00:00','is_admin'=>1]))->assertSessionHasNoErrors();
        $e=Estudiante::firstOrFail();
        $this->assertNotEquals(99999,$e->id);
        $this->assertNotEquals('2000-01-01',$e->created_at->format('Y-m-d'));
        $this->assertNotContains('is_admin',$e->getFillable());
    }
    public function test_cadena_sql_se_guarda_como_texto_y_no_se_ejecuta(): void {
        $texto="Ana'); DROP TABLE estudiantes; --";
        $this->post('/estudiantes',$this->datos(['nombres'=>$texto]))->assertSessionHasNoErrors();
        $this->assertTrue(Schema::hasTable('estudiantes'));
        $this->assertDatabaseHas('estudiantes',['nombres'=>$texto]);
    }
    public function test_blade_escapa_html_del_usuario(): void {
        $e=Estudiante::create($this->datos(['nombres'=>'<script>alert(1)</script>']));
        $this->get('/estudiantes/'.$e->id)->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;',false)->assertDontSee('<script>alert(1)</script>',false);
    }
    public function test_seeder_se_puede_repetir_sin_duplicar(): void {
        $this->seed(EstudianteSeeder::class); $this->seed(EstudianteSeeder::class);
        $this->assertDatabaseCount('estudiantes',8);
    }
}
