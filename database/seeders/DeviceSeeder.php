<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $devices = [
            ['device_name' => 'C577008',      'fleet' => 'ADW', 'sim' => '8115115756',  'imei' => '865847058955673'],
            ['device_name' => 'C577023AMM',   'fleet' => 'ADW', 'sim' => '8115116360',  'imei' => '862430065974608'],
            ['device_name' => 'C577024AMM',   'fleet' => 'ADW', 'sim' => '8115116380',  'imei' => '862430062300229'],
            ['device_name' => 'C577025AMM',   'fleet' => 'ADW', 'sim' => '8115116382',  'imei' => '862430065875367'],
            ['device_name' => 'C577026AMM',   'fleet' => 'ADW', 'sim' => '8115116335',  'imei' => '862430062300534'],
            ['device_name' => 'C577028',      'fleet' => 'ADW', 'sim' => '8115116414',  'imei' => '865847058963396'],
            ['device_name' => 'C577030AMM',   'fleet' => 'ADW', 'sim' => '8115116381',  'imei' => '862430065981231'],
            ['device_name' => 'C77023',       'fleet' => 'ADW', 'sim' => '8115115670',  'imei' => '867105075562149'],
            ['device_name' => 'C77025',       'fleet' => 'ADW', 'sim' => '8115115688',  'imei' => '867105075602069'],
            ['device_name' => 'C77026',       'fleet' => 'ADW', 'sim' => '8115115629',  'imei' => '867395078902208'],
            ['device_name' => 'C77028',       'fleet' => 'ADW', 'sim' => '8115116442',  'imei' => '867395078110398'],
            ['device_name' => 'C77029',       'fleet' => 'ADW', 'sim' => '8115116090',  'imei' => '867395078054661'],
            ['device_name' => 'C77030',       'fleet' => 'ADW', 'sim' => '8115115012',  'imei' => '867395078844632'],
            ['device_name' => 'C77031',       'fleet' => 'ADW', 'sim' => '8115115882',  'imei' => '867105075562123'],
            ['device_name' => 'C77032',       'fleet' => 'ADW', 'sim' => '8115116460',  'imei' => '867395078090590'],
            ['device_name' => 'C77033',       'fleet' => 'ADW', 'sim' => '8115116427',  'imei' => '867395078096217'],
            ['device_name' => 'C77034',       'fleet' => 'ADW', 'sim' => '8115116129',  'imei' => '867395078125024'],
            ['device_name' => 'C77049',       'fleet' => 'ADW', 'sim' => '8115116467',  'imei' => '867395078103278'],
            ['device_name' => 'C77050',       'fleet' => 'ADW', 'sim' => '8115116426',  'imei' => '867395078845951'],
            ['device_name' => 'C77051',       'fleet' => 'ADW', 'sim' => '8115116055',  'imei' => '867395078885312'],
            ['device_name' => 'C77052',       'fleet' => 'ADW', 'sim' => '8115115985',  'imei' => '867395078863376'],
            ['device_name' => 'C77053',       'fleet' => 'ADW', 'sim' => '8115116217',  'imei' => '867105075638121'],
            ['device_name' => 'C77054',       'fleet' => 'ADW', 'sim' => '8115116443',  'imei' => '867105075667815'],
            ['device_name' => 'C77065',       'fleet' => 'ADW', 'sim' => '8115116428',  'imei' => '867395078102155'],
            ['device_name' => 'C77066',       'fleet' => 'ADW', 'sim' => '8115115077',  'imei' => '867395078831399'],
            ['device_name' => 'C77067',       'fleet' => 'ADW', 'sim' => '8115114953',  'imei' => '867395078892094'],
            ['device_name' => 'C77068',       'fleet' => 'ADW', 'sim' => '8115116337',  'imei' => '865622078578411'],
            ['device_name' => 'DA40036',      'fleet' => 'ADW', 'sim' => '8115116201',  'imei' => '867105075667856'],
            ['device_name' => 'DA40037',      'fleet' => 'ADW', 'sim' => '8115116121',  'imei' => '867105075514397'],
            ['device_name' => 'DA40038',      'fleet' => 'ADW', 'sim' => '8115115787',  'imei' => '867105075666684'],
            ['device_name' => 'DA40039',      'fleet' => 'ADW', 'sim' => '8115116263',  'imei' => '867105075593250'],
            ['device_name' => 'FT2508',       'fleet' => 'ADW', 'sim' => '85193665564', 'imei' => '867395078847981'],
            ['device_name' => 'FT2512',       'fleet' => 'ADW', 'sim' => '85193665561', 'imei' => '867395076528005'],
            ['device_name' => 'FT2520',       'fleet' => 'ADW', 'sim' => '8115115701',  'imei' => '865847058987494'],
            ['device_name' => 'FT2525',       'fleet' => 'ADW', 'sim' => '85193665567', 'imei' => '865847058984921'],
            ['device_name' => 'FT2546',       'fleet' => 'ADW', 'sim' => '85193665569', 'imei' => '867395078831142'],
            ['device_name' => 'FT52530 AMM',  'fleet' => 'ADW', 'sim' => '8115116141',  'imei' => '867105075654797'],
            ['device_name' => 'H57049 AMM',   'fleet' => 'ADW', 'sim' => '8115115455',  'imei' => '862430062045790'],
            ['device_name' => 'H57054 AMM',   'fleet' => 'ADW', 'sim' => '8115115806',  'imei' => '867395076532080'],
            ['device_name' => 'H57055 AMM',   'fleet' => 'ADW', 'sim' => '8115115579',  'imei' => '867395076608518'],
            ['device_name' => 'HD78355',      'fleet' => 'ADW', 'sim' => '8115116473',  'imei' => '867395078875305'],
            ['device_name' => 'HD78356',      'fleet' => 'ADW', 'sim' => '8115115209',  'imei' => '867395078129075'],
            ['device_name' => 'HD78357',      'fleet' => 'ADW', 'sim' => '8115116487',  'imei' => '867395078892664'],
            ['device_name' => 'HD78358',      'fleet' => 'ADW', 'sim' => '8115114947',  'imei' => '867395078103443'],
            ['device_name' => 'HD78359',      'fleet' => 'ADW', 'sim' => '8115116043',  'imei' => '867395078856222'],
            ['device_name' => 'HD78360',      'fleet' => 'ADW', 'sim' => '8115115233',  'imei' => '867395078070477'],
            ['device_name' => 'HD78361',      'fleet' => 'ADW', 'sim' => '8115116486',  'imei' => '867395078869159'],
            ['device_name' => 'HD78362',      'fleet' => 'ADW', 'sim' => '8115115334',  'imei' => '867395078011513'],
            ['device_name' => 'HD78363',      'fleet' => 'ADW', 'sim' => '8115116461',  'imei' => '867395076616651'],
            ['device_name' => 'HD78364',      'fleet' => 'ADW', 'sim' => '8115116072',  'imei' => '867395078056518'],
            ['device_name' => 'HD78365',      'fleet' => 'ADW', 'sim' => '8115115294',  'imei' => '867395078094782'],
            ['device_name' => 'HD78380',      'fleet' => 'ADW', 'sim' => '8115115977',  'imei' => '867395078011257'],
            ['device_name' => 'HD78381',      'fleet' => 'ADW', 'sim' => '8115116404',  'imei' => '867395078030976'],
            ['device_name' => 'HD78382',      'fleet' => 'ADW', 'sim' => '8115116162',  'imei' => '867105075530757'],
            ['device_name' => 'HD78383',      'fleet' => 'ADW', 'sim' => '8115115722',  'imei' => '867395078892839'],
            ['device_name' => 'HD78384',      'fleet' => 'ADW', 'sim' => '8115115560',  'imei' => '867395078110166'],
            ['device_name' => 'HD78385',      'fleet' => 'ADW', 'sim' => '8115115153',  'imei' => '867395078047509'],
            ['device_name' => 'HD78386',      'fleet' => 'ADW', 'sim' => '8115116490',  'imei' => '867105075537067'],
            ['device_name' => 'HD78387',      'fleet' => 'ADW', 'sim' => '8115115563',  'imei' => '867395078816648'],
            ['device_name' => 'HD78401',      'fleet' => 'ADW', 'sim' => '8115115607',  'imei' => '867105075530658'],
            ['device_name' => 'HD78402',      'fleet' => 'ADW', 'sim' => '8115115180',  'imei' => '867395078813751'],
            ['device_name' => 'HD78403',      'fleet' => 'ADW', 'sim' => '8115115004',  'imei' => '867395078854185'],
            ['device_name' => 'HD78404',      'fleet' => 'ADW', 'sim' => '8115115573',  'imei' => '867395078070519'],
            ['device_name' => 'HD78405',      'fleet' => 'ADW', 'sim' => '8115116491',  'imei' => '867105075591171'],
            ['device_name' => 'HD78406',      'fleet' => 'ADW', 'sim' => '8115116407',  'imei' => '867395078123375'],
            ['device_name' => 'HD78407',      'fleet' => 'ADW', 'sim' => '8115115089',  'imei' => '867395078006232'],
            ['device_name' => 'HD78408',      'fleet' => 'ADW', 'sim' => '8115116422',  'imei' => '867395078892797'],
            ['device_name' => 'HD78409',      'fleet' => 'ADW', 'sim' => '8115116409',  'imei' => '867395078079320'],
            ['device_name' => 'HD78412',      'fleet' => 'ADW', 'sim' => '8115116413',  'imei' => '867395078121338'],
            ['device_name' => 'HD78413',      'fleet' => 'ADW', 'sim' => '8115116311',  'imei' => '867395078057854'],
            ['device_name' => 'HD78414',      'fleet' => 'ADW', 'sim' => '8115115530',  'imei' => '867395078103666'],
            ['device_name' => 'HD78415',      'fleet' => 'ADW', 'sim' => '8115115548',  'imei' => '867395078797632'],
            ['device_name' => 'HD78416',      'fleet' => 'ADW', 'sim' => '8115114939',  'imei' => '867395078820285'],
            ['device_name' => 'HD78417',      'fleet' => 'ADW', 'sim' => '8115115549',  'imei' => '867395078090582'],
            ['device_name' => 'HD78418',      'fleet' => 'ADW', 'sim' => '8115114925',  'imei' => '867395078000482'],
            ['device_name' => 'HD78432',      'fleet' => 'ADW', 'sim' => '8115115707',  'imei' => '867395078828296'],
            ['device_name' => 'HD78433',      'fleet' => 'ADW', 'sim' => '8115116244',  'imei' => '867395078858574'],
            ['device_name' => 'HD78434',      'fleet' => 'ADW', 'sim' => '8115116475',  'imei' => '867395078824238'],
            ['device_name' => 'HD78450',      'fleet' => 'ADW', 'sim' => '8115115603',  'imei' => '867105075514470'],
            ['device_name' => 'HD78454',      'fleet' => 'ADW', 'sim' => '8115116398',  'imei' => '867395078850795'],
            ['device_name' => 'HD78455',      'fleet' => 'ADW', 'sim' => '8115115879',  'imei' => '867395078849755'],
            ['device_name' => 'HD78456',      'fleet' => 'ADW', 'sim' => '8115116437',  'imei' => '867395078094345'],
            ['device_name' => 'HD78457',      'fleet' => 'ADW', 'sim' => '8115115578',  'imei' => '867395077971535'],
            ['device_name' => 'HD78468',      'fleet' => 'ADW', 'sim' => '8115116449',  'imei' => '867395078813207'],
            ['device_name' => 'HD78469',      'fleet' => 'ADW', 'sim' => '8115116384',  'imei' => '867105075593615'],
            ['device_name' => 'HD78470',      'fleet' => 'ADW', 'sim' => '8115115733',  'imei' => '867395078885023'],
            ['device_name' => 'HD78471',      'fleet' => 'ADW', 'sim' => '8115115778',  'imei' => '867395078892672'],
            ['device_name' => 'HD78472',      'fleet' => 'ADW', 'sim' => '8115115995',  'imei' => '867395078881162'],
            ['device_name' => 'HD78482',      'fleet' => 'ADW', 'sim' => '8115116484',  'imei' => '867395078819089'],
            ['device_name' => 'HD78483',      'fleet' => 'ADW', 'sim' => '8115116476',  'imei' => '867395078916919'],
            ['device_name' => 'HD78484',      'fleet' => 'ADW', 'sim' => '8115115467',  'imei' => '867395078056799'],
            ['device_name' => 'HD78485',      'fleet' => 'ADW', 'sim' => '8115115348',  'imei' => '867395078119290'],
            ['device_name' => 'HD78486',      'fleet' => 'ADW', 'sim' => '8115114922',  'imei' => '867105075667849'],
            ['device_name' => 'HD78487',      'fleet' => 'ADW', 'sim' => '8115116397',  'imei' => '867105075569284'],
            ['device_name' => 'HD78488',      'fleet' => 'ADW', 'sim' => '8115115448',  'imei' => '867395078128986'],
            ['device_name' => 'HD78489',      'fleet' => 'ADW', 'sim' => '8115115576',  'imei' => '867395078127269'],
            ['device_name' => 'HD78494',      'fleet' => 'ADW', 'sim' => '8115115327',  'imei' => '867395078845373'],
            ['device_name' => 'HD78495',      'fleet' => 'ADW', 'sim' => '8115115108',  'imei' => '867395078096142'],
            ['device_name' => 'HD78496',      'fleet' => 'ADW', 'sim' => '8115115353',  'imei' => '867395078029580'],
            ['device_name' => 'HD78497',      'fleet' => 'ADW', 'sim' => '8115115189',  'imei' => '867395078831308'],
            ['device_name' => 'HD78498',      'fleet' => 'ADW', 'sim' => '8115115269',  'imei' => '867395078852957'],
            ['device_name' => 'HD78499',      'fleet' => 'ADW', 'sim' => '8115116474',  'imei' => '867395078056468'],
            ['device_name' => 'HD78500',      'fleet' => 'ADW', 'sim' => '85114561284', 'imei' => '867395078037609'],
            ['device_name' => 'HD78501',      'fleet' => 'ADW', 'sim' => '8115115150',  'imei' => '867395078804255'],
            ['device_name' => 'HD78502',      'fleet' => 'ADW', 'sim' => '8115114930',  'imei' => '867395078123607'],
            ['device_name' => 'HD78503',      'fleet' => 'ADW', 'sim' => '8115116402',  'imei' => '867395078852544'],
            ['device_name' => 'HD78504',      'fleet' => 'ADW', 'sim' => '8115116396',  'imei' => '867105075667880'],
            ['device_name' => 'HD78505',      'fleet' => 'ADW', 'sim' => '8115114946',  'imei' => '867395078029309'],
            ['device_name' => 'HD78506',      'fleet' => 'ADW', 'sim' => '8115114945',  'imei' => '867395078853260'],
            ['device_name' => 'LT2503',       'fleet' => 'ADW', 'sim' => '8115114957',  'imei' => '867105075591478'],
            ['device_name' => 'LT2508',       'fleet' => 'ADW', 'sim' => '85193665570', 'imei' => '867395078829302'],
            ['device_name' => 'W57302AMM',    'fleet' => 'ADW', 'sim' => '8115116391',  'imei' => '861553074489481'],
            ['device_name' => 'W57305 AMM',   'fleet' => 'ADW', 'sim' => '851936656031','imei' => '865847058963115'],
            ['device_name' => 'WT2508',       'fleet' => 'ADW', 'sim' => '85193665568', 'imei' => '867395078831019'],
            ['device_name' => 'WT57301AMM',   'fleet' => 'ADW', 'sim' => '8115116195',  'imei' => '867105075602093'],
        ];

        $now = now();

        $devices = array_map(function ($device) use ($now) {
            return array_merge($device, [
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }, $devices);

        // Insert in chunks to avoid hitting placeholder/packet limits on large datasets
        foreach (array_chunk($devices, 100) as $chunk) {
            DB::table('icc_units')->insert($chunk);
        }
    }
}
