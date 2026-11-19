<?php
namespace Database\Seeders;
use App\Models\Estudiante;
use Illuminate\Database\Seeder;
class EstudianteSeeder extends Seeder {
    public function run(): void {
        // Datos ficticios. firstOrCreate evita duplicarlos al repetir el seeder.
        $datos = [
            ['Ana Lucía','Torres García','VI',17.50],['Diego','Ramírez López','V',15.80],
            ['María Fernanda','Castillo Pérez','VII',18.20],['Luis Alberto','Vega Díaz','IV',14.60],
            ['Camila','Rojas Mendoza','VI',16.90],['José Miguel','Flores Ruiz','VIII',17.10],
            ['Valeria','Sánchez León','III',19.00],['Andrés','Morales Silva','VI',15.40]
        ];
        foreach ($datos as $i=>$d) {
            Estudiante::firstOrCreate(['codigo'=>(string)(2026100001+$i)], [
                'nombres'=>$d[0], 'apellidos'=>$d[1], 'email'=>'estudiante'.($i+1).'@example.com',
                'ciclo'=>$d[2], 'promedio'=>$d[3]
            ]);
        }
    }
}
