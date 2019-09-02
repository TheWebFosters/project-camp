<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $customers = [
          ['id' => '1','company' => 'Web Experts','currency_id' => '1','tax_number' => '1010al','mobile' => '923232323','alternate_contact_no' => '923232322','email' => 'webexpert@test.com','website' => 'https://www.example.com','city' => 'Arizona','state' => 'Arizona','zip_code' => '1010101','country' => 'USA','billing_address' => 'Linking Street, Arizona','shipping_address' => 'Linking Street, Arizona','status_id' => '1','source_id' => null,'assigned_to' => null,'contacted_date' => null,'description' => null,'created_by' => '1','deleted_at' => null,'created_at' => '2019-04-18 12:20:13','updated_at' => '2019-04-18 12:25:09'],
          ['id' => '2','company' => 'Digital Ways','currency_id' => '1','tax_number' => null,'mobile' => '7890130490','alternate_contact_no' => null,'email' => 'digitalways@example.com','website' => null,'city' => null,'state' => null,'zip_code' => null,'country' => null,'billing_address' => null,'shipping_address' => null,'status_id' => '2','source_id' => '1','assigned_to' => '1','contacted_date' => '2019-07-26 13:25:00','description' => null,'created_by' => '1','deleted_at' => null,'created_at' => '2019-07-25 15:50:54','updated_at' => '2019-07-25 15:50:54']
        ];

        DB::table('customers')->insert($customers);
        
        $users = [
        ['id' => '1','name' => 'Superadmin','email' => 'admin@example.com','mobile' => '900909090','alternate_num' => '10190190','home_address' => null,'current_address' => null,'address' => null,'skype' => 'sky','linkedin' => 'www.linkedin.com','facebook' => 'www.fb.com','twitter' => 'www.twitter.com','birth_date' => '1985-01-01','guardian_name' => 'Test','gender' => 'male','account_holder_name' => null,'account_no' => null,'bank_name' => null,'bank_identifier_code' => null,'branch_location' => null,'tax_payer_id' => null,'note' => null,'password' => '$2y$10$IaqWf4d8u4F/qD6JDOBI5exa/nDOQVJsETM0KzSKMhVopn04mAwBS','created_by' => null,'customer_id' => null,'last_login' => '2019-04-18 12:03:18','active' => '2019-04-18 12:03:18','activation_key' => 'f25ea9af-9c92-4e3a-8a74-b6415e212ad9','remember_token' => 'rIA3hNFYTV', 'sticky_notes' => 'This is stick note area. Anything you write here is auto-saved','created_at' => '2019-04-18 12:03:19','updated_at' => '2019-04-18 12:35:11'],
        ['id' => '2','name' => 'Mike','email' => 'customer@example.com','mobile' => null,'alternate_num' => null,'home_address' => null,'current_address' => null,'address' => null,'skype' => null,'linkedin' => null,'facebook' => null,'twitter' => null,'birth_date' => null,'guardian_name' => null,'gender' => null,'account_holder_name' => null,'account_no' => null,'bank_name' => null,'bank_identifier_code' => null,'branch_location' => null,'tax_payer_id' => null,'note' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.','password' => '$2y$10$IaqWf4d8u4F/qD6JDOBI5exa/nDOQVJsETM0KzSKMhVopn04mAwBS','created_by' => '1','customer_id' => '1','last_login' => null,'active' => null,'activation_key' => null,'remember_token' => null, 'sticky_notes' => 'This is stick note area. Anything you write here is auto-saved','created_at' => '2019-04-18 12:26:19','updated_at' => '2019-04-18 12:26:19'],
        ['id' => '3','name' => 'John Tyson','email' => 'digitalways@example.com','mobile' => null,'alternate_num' => null,'home_address' => null,'current_address' => null,'address' => null,'skype' => null,'linkedin' => null,'facebook' => null,'twitter' => null,'birth_date' => null,'guardian_name' => null,'gender' => null,'account_holder_name' => null,'account_no' => null,'bank_name' => null,'bank_identifier_code' => null,'branch_location' => null,'tax_payer_id' => null,'note' => null,'password' => null,'created_by' => null,'customer_id' => '2','last_login' => null,'active' => null,'activation_key' => null,'remember_token' => null,'sticky_notes' => 'This is stick note area. Anything you write here is auto-saved','created_at' => '2019-07-25 15:50:55','updated_at' => '2019-07-25 15:50:55'],
        ['id' => '4','name' => 'Donald Black','email' => 'employee@example.com','mobile' => null,'alternate_num' => null,'home_address' => null,'current_address' => null,'address' => null,'skype' => null,'linkedin' => null,'facebook' => null,'twitter' => null,'birth_date' => null,'guardian_name' => null,'gender' => null,'account_holder_name' => null,'account_no' => null,'bank_name' => null,'bank_identifier_code' => null,'branch_location' => null,'tax_payer_id' => null,'note' => null,'password' => '$2y$10$IaqWf4d8u4F/qD6JDOBI5exa/nDOQVJsETM0KzSKMhVopn04mAwBS','created_by' => null,'customer_id' => null,'last_login' => null,'active' => '2019-07-30 00:00:00','activation_key' => null,'remember_token' => null,'sticky_notes' => 'This is stick note area. Anything you write here is auto-saved','created_at' => '2019-07-30 07:45:43','updated_at' => '2019-07-30 07:45:43']
      ];

        DB::table('users')->insert($users);

        $systems = [
            ['key' => 'tax_number','value' => null],
            ['key' => 'mobile','value' => '94376000000'],
            ['key' => 'alternate_contact_no','value' => null],
            ['key' => 'email','value' => 'test@test.com'],
            ['key' => 'city','value' => null],
            ['key' => 'state','value' => null],
            ['key' => 'country','value' => null],
            ['key' => 'zip_code','value' => null],
            ['key' => 'date_format','value' => 'm-d-Y'],
            ['key' => 'time_format','value' => '12'],
            ['key' => 'currency_id','value' => '1']
          ];

        DB::table('systems')->insert($systems);

        $permissions = [
            ['id' => '1','name' => 'employee.create','guard_name' => 'web','created_at' => '2019-04-18 12:03:15','updated_at' => '2019-04-18 12:03:15'],
            ['id' => '2','name' => 'employee.view','guard_name' => 'web','created_at' => '2019-04-18 12:03:16','updated_at' => '2019-04-18 12:03:16'],
            ['id' => '3','name' => 'employee.edit','guard_name' => 'web','created_at' => '2019-04-18 12:03:16','updated_at' => '2019-04-18 12:03:16'],
            ['id' => '4','name' => 'employee.delete','guard_name' => 'web','created_at' => '2019-04-18 12:03:16','updated_at' => '2019-04-18 12:03:16'],
            ['id' => '5','name' => 'employeeNote.create','guard_name' => 'web','created_at' => '2019-04-18 12:03:16','updated_at' => '2019-04-18 12:03:16'],
            ['id' => '6','name' => 'employeeNote.view','guard_name' => 'web','created_at' => '2019-04-18 12:03:16','updated_at' => '2019-04-18 12:03:16'],
            ['id' => '7','name' => 'employeeNote.edit','guard_name' => 'web','created_at' => '2019-04-18 12:03:16','updated_at' => '2019-04-18 12:03:16'],
            ['id' => '8','name' => 'employeeNote.delete','guard_name' => 'web','created_at' => '2019-04-18 12:03:16','updated_at' => '2019-04-18 12:03:16'],
            ['id' => '9','name' => 'customer.create','guard_name' => 'web','created_at' => '2019-04-18 12:03:16','updated_at' => '2019-04-18 12:03:16'],
            ['id' => '10','name' => 'customer.view','guard_name' => 'web','created_at' => '2019-04-18 12:03:16','updated_at' => '2019-04-18 12:03:16'],
            ['id' => '11','name' => 'customer.edit','guard_name' => 'web','created_at' => '2019-04-18 12:03:16','updated_at' => '2019-04-18 12:03:16'],
            ['id' => '12','name' => 'customer.delete','guard_name' => 'web','created_at' => '2019-04-18 12:03:16','updated_at' => '2019-04-18 12:03:16'],
            ['id' => '13','name' => 'contact.create','guard_name' => 'web','created_at' => '2019-04-18 12:03:16','updated_at' => '2019-04-18 12:03:16'],
            ['id' => '14','name' => 'contact.view','guard_name' => 'web','created_at' => '2019-04-18 12:03:17','updated_at' => '2019-04-18 12:03:17'],
            ['id' => '15','name' => 'contact.edit','guard_name' => 'web','created_at' => '2019-04-18 12:03:17','updated_at' => '2019-04-18 12:03:17'],
            ['id' => '16','name' => 'contact.delete','guard_name' => 'web','created_at' => '2019-04-18 12:03:17','updated_at' => '2019-04-18 12:03:17'],
            ['id' => '17','name' => 'customerNote.create','guard_name' => 'web','created_at' => '2019-04-18 12:03:17','updated_at' => '2019-04-18 12:03:17'],
            ['id' => '18','name' => 'customerNote.view','guard_name' => 'web','created_at' => '2019-04-18 12:03:17','updated_at' => '2019-04-18 12:03:17'],
            ['id' => '19','name' => 'customerNote.edit','guard_name' => 'web','created_at' => '2019-04-18 12:03:17','updated_at' => '2019-04-18 12:03:17'],
            ['id' => '20','name' => 'customerNote.delete','guard_name' => 'web','created_at' => '2019-04-18 12:03:17','updated_at' => '2019-04-18 12:03:17'],
            ['id' => '21','name' => 'project.create','guard_name' => 'web','created_at' => '2019-04-18 12:03:17','updated_at' => '2019-04-18 12:03:17'],
            ['id' => '22','name' => 'file.create','guard_name' => 'web','created_at' => '2019-04-18 12:03:18','updated_at' => '2019-04-18 12:03:18'],
            ['id' => '23','name' => 'setting','guard_name' => 'web','created_at' => '2019-04-18 12:03:18','updated_at' => '2019-04-18 12:03:18'],
            ['id' => '24','name' => 'profile.edit','guard_name' => 'web','created_at' => '2019-04-18 12:03:18','updated_at' => '2019-04-18 12:03:18'],
            ['id' => '25','name' => 'sales.invoices','guard_name' => 'web','created_at' => '2019-04-18 12:03:18','updated_at' => '2019-04-18 12:03:18'],
            ['id' => '26','name' => 'project.1.edit','guard_name' => 'web','created_at' => '2019-04-18 12:27:30','updated_at' => '2019-04-18 12:27:30'],
            ['id' => '27','name' => 'project.1.status','guard_name' => 'web','created_at' => '2019-04-18 12:27:30','updated_at' => '2019-04-18 12:27:30'],
            ['id' => '28','name' => 'project.1.delete','guard_name' => 'web','created_at' => '2019-04-18 12:27:30','updated_at' => '2019-04-18 12:27:30'],
            ['id' => '29','name' => 'project.1.overview','guard_name' => 'web','created_at' => '2019-04-18 12:27:30','updated_at' => '2019-04-18 12:27:30'],
            ['id' => '30','name' => 'project.1.activities','guard_name' => 'web','created_at' => '2019-04-18 12:27:30','updated_at' => '2019-04-18 12:27:30'],
            ['id' => '31','name' => 'project.1.task.create','guard_name' => 'web','created_at' => '2019-04-18 12:27:30','updated_at' => '2019-04-18 12:27:30'],
            ['id' => '32','name' => 'project.1.task.view','guard_name' => 'web','created_at' => '2019-04-18 12:27:30','updated_at' => '2019-04-18 12:27:30'],
            ['id' => '33','name' => 'project.1.task.edit','guard_name' => 'web','created_at' => '2019-04-18 12:27:30','updated_at' => '2019-04-18 12:27:30'],
            ['id' => '34','name' => 'project.1.task.delete','guard_name' => 'web','created_at' => '2019-04-18 12:27:30','updated_at' => '2019-04-18 12:27:30'],
            ['id' => '35','name' => 'project.1.milestone.create','guard_name' => 'web','created_at' => '2019-04-18 12:27:30','updated_at' => '2019-04-18 12:27:30'],
            ['id' => '36','name' => 'project.1.milestone.view','guard_name' => 'web','created_at' => '2019-04-18 12:27:30','updated_at' => '2019-04-18 12:27:30'],
            ['id' => '37','name' => 'project.1.milestone.edit','guard_name' => 'web','created_at' => '2019-04-18 12:27:30','updated_at' => '2019-04-18 12:27:30'],
            ['id' => '38','name' => 'project.1.milestone.delete','guard_name' => 'web','created_at' => '2019-04-18 12:27:30','updated_at' => '2019-04-18 12:27:30'],
            ['id' => '39','name' => 'project.1.invoice.create','guard_name' => 'web','created_at' => '2019-04-18 12:27:30','updated_at' => '2019-04-18 12:27:30'],
            ['id' => '40','name' => 'project.1.invoice.view','guard_name' => 'web','created_at' => '2019-04-18 12:27:30','updated_at' => '2019-04-18 12:27:30'],
            ['id' => '41','name' => 'project.1.invoice.edit','guard_name' => 'web','created_at' => '2019-04-18 12:27:30','updated_at' => '2019-04-18 12:27:30'],
            ['id' => '42','name' => 'project.1.invoice.delete','guard_name' => 'web','created_at' => '2019-04-18 12:27:30','updated_at' => '2019-04-18 12:27:30'],
            ['id' => '43','name' => 'project.2.edit','guard_name' => 'web','created_at' => '2019-04-18 12:28:58','updated_at' => '2019-04-18 12:28:58'],
            ['id' => '44','name' => 'project.2.status','guard_name' => 'web','created_at' => '2019-04-18 12:28:58','updated_at' => '2019-04-18 12:28:58'],
            ['id' => '45','name' => 'project.2.delete','guard_name' => 'web','created_at' => '2019-04-18 12:28:58','updated_at' => '2019-04-18 12:28:58'],
            ['id' => '46','name' => 'project.2.overview','guard_name' => 'web','created_at' => '2019-04-18 12:28:58','updated_at' => '2019-04-18 12:28:58'],
            ['id' => '47','name' => 'project.2.activities','guard_name' => 'web','created_at' => '2019-04-18 12:28:58','updated_at' => '2019-04-18 12:28:58'],
            ['id' => '48','name' => 'project.2.task.create','guard_name' => 'web','created_at' => '2019-04-18 12:28:58','updated_at' => '2019-04-18 12:28:58'],
            ['id' => '49','name' => 'project.2.task.view','guard_name' => 'web','created_at' => '2019-04-18 12:28:58','updated_at' => '2019-04-18 12:28:58'],
            ['id' => '50','name' => 'project.2.task.edit','guard_name' => 'web','created_at' => '2019-04-18 12:28:58','updated_at' => '2019-04-18 12:28:58'],
            ['id' => '51','name' => 'project.2.task.delete','guard_name' => 'web','created_at' => '2019-04-18 12:28:58','updated_at' => '2019-04-18 12:28:58'],
            ['id' => '52','name' => 'project.2.milestone.create','guard_name' => 'web','created_at' => '2019-04-18 12:28:58','updated_at' => '2019-04-18 12:28:58'],
            ['id' => '53','name' => 'project.2.milestone.view','guard_name' => 'web','created_at' => '2019-04-18 12:28:58','updated_at' => '2019-04-18 12:28:58'],
            ['id' => '54','name' => 'project.2.milestone.edit','guard_name' => 'web','created_at' => '2019-04-18 12:28:58','updated_at' => '2019-04-18 12:28:58'],
            ['id' => '55','name' => 'project.2.milestone.delete','guard_name' => 'web','created_at' => '2019-04-18 12:28:58','updated_at' => '2019-04-18 12:28:58'],
            ['id' => '56','name' => 'project.2.invoice.create','guard_name' => 'web','created_at' => '2019-04-18 12:28:58','updated_at' => '2019-04-18 12:28:58'],
            ['id' => '57','name' => 'project.2.invoice.view','guard_name' => 'web','created_at' => '2019-04-18 12:28:58','updated_at' => '2019-04-18 12:28:58'],
            ['id' => '58','name' => 'project.2.invoice.edit','guard_name' => 'web','created_at' => '2019-04-18 12:28:58','updated_at' => '2019-04-18 12:28:58'],
            ['id' => '59','name' => 'project.2.invoice.delete','guard_name' => 'web','created_at' => '2019-04-18 12:28:58','updated_at' => '2019-04-18 12:28:58'],
            ['id' => '94','name' => 'knowledge_base.view','guard_name' => 'web','created_at' => '2019-04-24 06:52:29','updated_at' => '2019-04-24 06:52:29'],
            ['id' => '95','name' => 'knowledge_base.create','guard_name' => 'web','created_at' => '2019-04-24 06:52:48','updated_at' => '2019-04-24 06:52:48'],
            ['id' => '96','name' => 'knowledge_base.edit','guard_name' => 'web','created_at' => '2019-04-24 06:53:05','updated_at' => '2019-04-24 06:53:05'],
            ['id' => '97','name' => 'knowledge_base.delete','guard_name' => 'web','created_at' => '2019-04-24 06:53:20','updated_at' => '2019-04-24 06:53:20'],
            ['id' => '115','name' => 'expense.create','guard_name' => 'web','created_at' => '2019-05-10 08:21:37','updated_at' => '2019-05-10 08:21:37'],
            ['id' => '116','name' => 'expense.edit','guard_name' => 'web','created_at' => '2019-05-10 08:21:59','updated_at' => '2019-05-10 08:21:59'],
            ['id' => '117','name' => 'expense.delete','guard_name' => 'web','created_at' => '2019-05-10 08:22:18','updated_at' => '2019-05-10 08:22:18'],
            ['id' => '118','name' => 'leaves.create','guard_name' => 'web','created_at' => '2019-05-14 04:49:33','updated_at' => '2019-05-14 04:49:33'],
            ['id' => '119','name' => 'leaves.edit','guard_name' => 'web','created_at' => '2019-05-14 04:49:49','updated_at' => '2019-05-14 04:49:49'],
            ['id' => '120','name' => 'leaves.delete','guard_name' => 'web','created_at' => '2019-05-14 04:50:03','updated_at' => '2019-05-14 04:50:03'],
            ['id' => '121','name' => 'tickets.create','guard_name' => 'web','created_at' => '2019-08-05 18:39:08','updated_at' => '2019-08-05 18:39:08'],
            ['id' => '122','name' => 'tickets.view','guard_name' => 'web','created_at' => '2019-08-05 18:39:13','updated_at' => '2019-08-05 18:39:13'],
            ['id' => '123','name' => 'tickets.edit','guard_name' => 'web','created_at' => '2019-08-05 18:39:18','updated_at' => '2019-08-05 18:39:18'],
            ['id' => '124','name' => 'tickets.delete','guard_name' => 'web','created_at' => '2019-08-05 18:39:21','updated_at' => '2019-08-05 18:39:21']
          ];


        DB::table('permissions')->insert($permissions);

        $roles = [
          ['id' => '1','name' => 'superadmin','guard_name' => 'web','type' => null,'created_at' => '2019-04-18 12:03:18','updated_at' => '2019-04-18 12:03:18'],
          ['id' => '2','name' => 'employee','guard_name' => 'web','type' => 'employee','created_at' => '2019-04-18 12:03:18','updated_at' => '2019-04-18 12:03:18'],
          ['id' => '3','name' => 'contact','guard_name' => 'web','type' => 'contact','created_at' => '2019-04-18 12:03:18','updated_at' => '2019-04-18 12:03:18'],
          ['id' => '4','name' => 'lead.1','guard_name' => 'web','type' => 'project','created_at' => '2019-04-18 12:27:30','updated_at' => '2019-04-18 12:27:30'],
          ['id' => '5','name' => 'member.1','guard_name' => 'web','type' => 'project','created_at' => '2019-04-18 12:27:30','updated_at' => '2019-04-18 12:27:30'],
          ['id' => '6','name' => 'customer.1','guard_name' => 'web','type' => 'project','created_at' => '2019-04-18 12:27:30','updated_at' => '2019-04-18 12:27:30'],
          ['id' => '7','name' => 'lead.2','guard_name' => 'web','type' => 'project','created_at' => '2019-04-18 12:28:58','updated_at' => '2019-04-18 12:28:58'],
          ['id' => '8','name' => 'member.2','guard_name' => 'web','type' => 'project','created_at' => '2019-04-18 12:28:59','updated_at' => '2019-04-18 12:28:59'],
          ['id' => '9','name' => 'customer.2','guard_name' => 'web','type' => 'project','created_at' => '2019-04-18 12:28:59','updated_at' => '2019-04-18 12:28:59']
        ];

        DB::table('roles')->insert($roles);

        $role_has_permissions = [
          ['permission_id' => '24','role_id' => '2'],
          ['permission_id' => '94','role_id' => '2'],
          ['permission_id' => '118','role_id' => '2'],
          ['permission_id' => '119','role_id' => '2'],
          ['permission_id' => '24','role_id' => '3'],
          ['permission_id' => '121','role_id' => '3'],
          ['permission_id' => '122','role_id' => '3'],
          ['permission_id' => '26','role_id' => '4'],
          ['permission_id' => '27','role_id' => '4'],
          ['permission_id' => '28','role_id' => '4'],
          ['permission_id' => '29','role_id' => '4'],
          ['permission_id' => '30','role_id' => '4'],
          ['permission_id' => '31','role_id' => '4'],
          ['permission_id' => '32','role_id' => '4'],
          ['permission_id' => '33','role_id' => '4'],
          ['permission_id' => '34','role_id' => '4'],
          ['permission_id' => '35','role_id' => '4'],
          ['permission_id' => '36','role_id' => '4'],
          ['permission_id' => '37','role_id' => '4'],
          ['permission_id' => '38','role_id' => '4'],
          ['permission_id' => '39','role_id' => '4'],
          ['permission_id' => '40','role_id' => '4'],
          ['permission_id' => '41','role_id' => '4'],
          ['permission_id' => '42','role_id' => '4'],
          ['permission_id' => '29','role_id' => '5'],
          ['permission_id' => '30','role_id' => '5'],
          ['permission_id' => '31','role_id' => '5'],
          ['permission_id' => '32','role_id' => '5'],
          ['permission_id' => '33','role_id' => '5'],
          ['permission_id' => '34','role_id' => '5'],
          ['permission_id' => '35','role_id' => '5'],
          ['permission_id' => '36','role_id' => '5'],
          ['permission_id' => '37','role_id' => '5'],
          ['permission_id' => '38','role_id' => '5'],
          ['permission_id' => '29','role_id' => '6'],
          ['permission_id' => '30','role_id' => '6'],
          ['permission_id' => '31','role_id' => '6'],
          ['permission_id' => '32','role_id' => '6'],
          ['permission_id' => '33','role_id' => '6'],
          ['permission_id' => '34','role_id' => '6'],
          ['permission_id' => '35','role_id' => '6'],
          ['permission_id' => '36','role_id' => '6'],
          ['permission_id' => '37','role_id' => '6'],
          ['permission_id' => '38','role_id' => '6'],
          ['permission_id' => '40','role_id' => '6'],
          ['permission_id' => '43','role_id' => '7'],
          ['permission_id' => '44','role_id' => '7'],
          ['permission_id' => '45','role_id' => '7'],
          ['permission_id' => '46','role_id' => '7'],
          ['permission_id' => '47','role_id' => '7'],
          ['permission_id' => '48','role_id' => '7'],
          ['permission_id' => '49','role_id' => '7'],
          ['permission_id' => '50','role_id' => '7'],
          ['permission_id' => '51','role_id' => '7'],
          ['permission_id' => '52','role_id' => '7'],
          ['permission_id' => '53','role_id' => '7'],
          ['permission_id' => '54','role_id' => '7'],
          ['permission_id' => '55','role_id' => '7'],
          ['permission_id' => '56','role_id' => '7'],
          ['permission_id' => '57','role_id' => '7'],
          ['permission_id' => '58','role_id' => '7'],
          ['permission_id' => '59','role_id' => '7'],
          ['permission_id' => '46','role_id' => '8'],
          ['permission_id' => '47','role_id' => '8'],
          ['permission_id' => '48','role_id' => '8'],
          ['permission_id' => '49','role_id' => '8'],
          ['permission_id' => '50','role_id' => '8'],
          ['permission_id' => '51','role_id' => '8'],
          ['permission_id' => '52','role_id' => '8'],
          ['permission_id' => '53','role_id' => '8'],
          ['permission_id' => '54','role_id' => '8'],
          ['permission_id' => '55','role_id' => '8'],
          ['permission_id' => '46','role_id' => '9'],
          ['permission_id' => '47','role_id' => '9'],
          ['permission_id' => '48','role_id' => '9'],
          ['permission_id' => '49','role_id' => '9'],
          ['permission_id' => '50','role_id' => '9'],
          ['permission_id' => '51','role_id' => '9'],
          ['permission_id' => '52','role_id' => '9'],
          ['permission_id' => '53','role_id' => '9'],
          ['permission_id' => '54','role_id' => '9'],
          ['permission_id' => '55','role_id' => '9'],
          ['permission_id' => '57','role_id' => '9']
        ];

        DB::table('role_has_permissions')->insert($role_has_permissions);

        $model_has_roles = [
          ['role_id' => '1','model_type' => 'App\\Components\\User\\Models\\User','model_id' => '1'],
          ['role_id' => '4','model_type' => 'App\\Components\\User\\Models\\User','model_id' => '1'],
          ['role_id' => '5','model_type' => 'App\\Components\\User\\Models\\User','model_id' => '1'],
          ['role_id' => '7','model_type' => 'App\\Components\\User\\Models\\User','model_id' => '1'],
          ['role_id' => '8','model_type' => 'App\\Components\\User\\Models\\User','model_id' => '1'],
          ['role_id' => '3','model_type' => 'App\\Components\\User\\Models\\User','model_id' => '2'],
          ['role_id' => '6','model_type' => 'App\\Components\\User\\Models\\User','model_id' => '2'],
          ['role_id' => '9','model_type' => 'App\\Components\\User\\Models\\User','model_id' => '2'],
          ['role_id' => '3','model_type' => 'App\\Components\\User\\Models\\User','model_id' => '3'],
          ['role_id' => '8','model_type' => 'App\\Components\\User\\Models\\User','model_id' => '4']
        ];

        DB::table('model_has_roles')->insert($model_has_roles);

        $statuses = [
            ['id' => '2','name' => 'New','created_at' => '2019-07-25 15:49:13','updated_at' => '2019-07-25 15:49:13'],
            ['id' => '3','name' => 'Working','created_at' => '2019-07-25 15:49:13','updated_at' => '2019-07-25 15:49:13'],
            ['id' => '4','name' => 'Open','created_at' => '2019-07-25 15:49:13','updated_at' => '2019-07-25 15:49:13'],
            ['id' => '5','name' => 'Qualified','created_at' => '2019-07-25 15:49:13','updated_at' => '2019-07-25 15:49:13'],
            ['id' => '6','name' => 'Unqualified','created_at' => '2019-07-25 15:49:13','updated_at' => '2019-07-25 15:49:13']
          ];

        DB::table('statuses')->insert($statuses);

        $sources = [
          ['id' => '1','name' => 'LinkedIn','created_at' => '2019-07-25 15:48:25','updated_at' => '2019-07-25 15:48:25'],
          ['id' => '2','name' => 'Facebook','created_at' => '2019-07-25 15:48:37','updated_at' => '2019-07-25 15:48:37'],
          ['id' => '3','name' => 'Twitter','created_at' => '2019-07-25 15:48:52','updated_at' => '2019-07-25 15:48:52'],
          ['id' => '4','name' => 'Trade Show','created_at' => '2019-07-25 15:48:52','updated_at' => '2019-07-25 15:48:52'],
          ['id' => '5','name' => 'Referral','created_at' => '2019-07-25 15:48:52','updated_at' => '2019-07-25 15:48:52']
        ];

        DB::table('sources')->insert($sources);

        $reminders = [
            ['id' => '1','remindable_id' => '2','remindable_type' => 'App\\Customer','remind_for' => 'Talk to Lead (Digital Ways)','remind_on' => '2019-07-26 13:00:00','remind_to' => '1','send_email' => '1','notes' => 'There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don\'t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn\'t anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet.','created_by' => '1','created_at' => '2019-07-25 15:56:17','updated_at' => '2019-07-25 15:56:17']
          ];

        DB::table('reminders')->insert($reminders);

        $currencies = [
          ['id' => '1','iso_name' => 'USD','symbol' => '$','created_at' => '2019-04-18 12:19:26','updated_at' => '2019-04-18 12:19:26']
        ];

        DB::table('currencies')->insert($currencies);
        
        $projects = [
          ['id' => '1','name' => 'Musical Design','customer_id' => '1','billing_type' => 'fixed_rate','total_rate' => '0.00','price_per_hours' => '0.00','estimated_hours' => '0.00','estimated_cost' => '0.00','status' => 'not_started','lead_id' => '1','start_date' => '2019-04-18 00:00:00','end_date' => '2019-06-08 00:00:00','description' => '<p class="ql-align-justify">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don\'t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn\'t anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.</p><p><br></p>','created_by' => '1','favorite' => '0','created_at' => '2019-04-18 12:27:30','updated_at' => '2019-04-18 12:27:30'],
          ['id' => '2','name' => 'Zynga Game','customer_id' => '1','billing_type' => 'fixed_rate','total_rate' => '0.00','price_per_hours' => '0.00','estimated_hours' => '0.00','estimated_cost' => '0.00','status' => 'in_progress','lead_id' => '1','start_date' => '2019-04-18 00:00:00','end_date' => '2019-06-15 00:00:00','description' => '<p class="ql-align-justify">The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from "de Finibus Bonorum et Malorum" by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.</p><p><br></p>','created_by' => '1','favorite' => '0','created_at' => '2019-04-18 12:28:58','updated_at' => '2019-04-18 12:28:58']
        ];

        DB::table('projects')->insert($projects);

        $project_members = [
          ['id' => '1','project_id' => '1','user_id' => '1'],
          ['id' => '2','project_id' => '2','user_id' => '1'],
          ['id' => '3','project_id' => '2','user_id' => '4']
        ];

        DB::table('project_members')->insert($project_members);

        $project_tasks = [
          ['id' => '1','project_id' => '2','task_id' => '#1','category_id' => null,'subject' => 'Cras dictum libero eget erat rhoncus fermentum.','hourly_rate' => '0.00','start_date' => '2019-04-18 00:00:00','due_date' => '2019-05-17 00:00:00','priority' => 'medium','description' => '<p class="ql-align-justify">The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from "de Finibus Bonorum et Malorum" by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.</p><p><br></p>','created_by' => '1','show_to_customer' => '1','is_completed' => '0','created_at' => '2019-04-18 12:30:49','updated_at' => '2019-04-18 12:32:05'],
          ['id' => '2','project_id' => '1','task_id' => '#2','category_id' => null,'subject' => 'Song recording','hourly_rate' => '0.00','start_date' => '2019-04-18 00:00:00','due_date' => '2019-04-20 00:00:00','priority' => 'high','description' => null,'created_by' => '1','show_to_customer' => '0','is_completed' => '0','created_at' => '2019-04-18 12:36:07','updated_at' => '2019-04-18 12:36:07'],
          ['id' => '3','project_id' => '2','task_id' => '#3','category_id' => '20','subject' => 'implementing algorithem for game','hourly_rate' => '0.00','start_date' => '2019-05-16 00:00:00','due_date' => '2019-05-15 00:00:00','priority' => 'medium','description' => '<h1 class="ql-align-justify">Minimax Algorithm in Game Theory | Set 1 (Introduction)</h1><p class="ql-align-justify"><br></p><p>Minimax is a kind of&nbsp;<a href="https://www.geeksforgeeks.org/tag/backtracking/" target="_blank" style="color: rgb(236, 78, 32);">backtracking</a>&nbsp;algorithm that is used in decision making and game theory to find the optimal move for a player, assuming that your opponent also plays optimally. It is widely used in two player turn-based games such as Tic-Tac-Toe, Backgammon, Mancala, Chess, etc.</p><p>In Minimax the two players are called maximizer and minimizer. The&nbsp;<strong>maximizer</strong>&nbsp;tries to get the highest score possible while the&nbsp;<strong>minimizer</strong>&nbsp;tries to do the opposite and get the lowest score possible.</p><p class="ql-align-justify">Every board state has a value associated with it. In a given state if the maximizer has upper hand then, the score of the board will tend to be some positive value. If the minimizer has the upper hand in that board state then it will tend to be some negative value. The values of the board are calculated by some heuristics which are unique for every type of game.</p><p class="ql-align-justify"><br></p><p><a href="https://www.geeksforgeeks.org/minimax-algorithm-in-game-theory-set-1-introduction/" target="_blank"><strong><em>to read more...</em></strong></a></p>','created_by' => '1','show_to_customer' => '0','is_completed' => '0','created_at' => '2019-05-08 05:49:06','updated_at' => '2019-05-08 05:49:06']
        ];

        DB::table('project_tasks')->insert($project_tasks);

        $project_task_members = [
          ['id' => '1','project_task_id' => '1','user_id' => '1'],
          ['id' => '2','project_task_id' => '2','user_id' => '1'],
          ['id' => '3','project_task_id' => '3','user_id' => '1'],
          ['id' => '4','project_task_id' => '1','user_id' => '4'],
        ];

        DB::table('project_task_members')->insert($project_task_members);

        $leaves = [
          ['id' => '1','user_id' => '1','start_date' => '2019-05-17 00:00:00','end_date' => '2019-05-20 00:00:00','status' => 'cancelled','reason' => 'Going home.','created_at' => '2019-05-16 10:51:35','updated_at' => '2019-05-16 10:51:43'],
          ['id' => '2','user_id' => '1','start_date' => '2019-04-03 00:00:00','end_date' => '2019-04-10 00:00:00','status' => 'approved','reason' => 'Going to attend meeting.','created_at' => '2019-05-16 10:52:34','updated_at' => '2019-05-16 10:52:54']
        ];

        DB::table('leaves')->insert($leaves);
        
        $notifications = [
          ['id' => '0de6ddeb-4397-4766-b219-17d2df41eee7','type' => 'App\\Notifications\\LeaveApplied','notifiable_type' => 'App\\Components\\User\\Models\\User','notifiable_id' => '1','data' => '{"user_id":1,"leave_id":1}','read_at' => '2019-05-16 10:53:39','created_at' => '2019-05-16 10:52:35','updated_at' => '2019-05-16 10:53:39'],
          ['id' => '2063de03-b011-4231-81d2-75d2ca8547ae','type' => 'App\\Notifications\\TaskCreatedNotification','notifiable_type' => 'App\\Components\\User\\Models\\User','notifiable_id' => '1','data' => '{"project_id":1,"task_id":2}','read_at' => '2019-04-18 12:36:17','created_at' => '2019-04-18 12:36:07','updated_at' => '2019-04-18 12:36:17'],
          ['id' => '381bf190-30eb-465e-94a7-3ec7743e588e','type' => 'App\\Notifications\\ProjectCreatedNotification','notifiable_type' => 'App\\Components\\User\\Models\\User','notifiable_id' => '1','data' => '{"project_id":2}','read_at' => '2019-04-18 12:30:49','created_at' => '2019-04-18 12:28:59','updated_at' => '2019-04-18 12:36:17'],
          ['id' => '3827d90e-edbe-4ab3-99bc-8e0fdf59c947','type' => 'App\\Notifications\\TaskCreatedNotification','notifiable_type' => 'App\\Components\\User\\Models\\User','notifiable_id' => '1','data' => '{"project_id":2,"task_id":3}','read_at' => '2019-05-16 10:53:39','created_at' => '2019-05-08 05:49:06','updated_at' => '2019-05-16 10:53:39'],
          ['id' => '498e054f-1e98-4019-88bf-d4807b116dc1','type' => 'App\\Notifications\\ProjectCreatedNotification','notifiable_type' => 'App\\Components\\User\\Models\\User','notifiable_id' => '1','data' => '{"project_id":1}','read_at' => '2019-04-18 12:36:17','created_at' => '2019-04-18 12:27:31','updated_at' => '2019-04-18 12:36:17'],
          ['id' => '4dc215b9-7ac8-4ef3-ac35-ebcbfcba5b7b','type' => 'App\\Notifications\\LeaveResponded','notifiable_type' => 'App\\Components\\User\\Models\\User','notifiable_id' => '1','data' => '{"leave_id":2,"admin":1}','read_at' => '2019-08-02 17:57:53','created_at' => '2019-05-16 10:52:54','updated_at' => '2019-08-02 17:57:53'],
          ['id' => '55034e68-b333-4a3b-b5ce-a98754a5667a','type' => 'App\\Notifications\\TicketCreated','notifiable_type' => 'App\\Components\\User\\Models\\User','notifiable_id' => '4','data' => '{"created_by":1,"ticket_id":1}','read_at' => null,'created_at' => '2019-08-13 11:05:32','updated_at' => '2019-08-13 11:05:32'],
          ['id' => '5b256fb6-da16-4c05-bd81-f1bd63ea2cae','type' => 'App\\Notifications\\TaskCreatedNotification','notifiable_type' => 'App\\Components\\User\\Models\\User','notifiable_id' => '1','data' => '{"project_id":2,"task_id":1}','read_at' => '2019-04-18 12:30:49','created_at' => '2019-04-18 12:30:49','updated_at' => '2019-04-18 12:36:17'],
          ['id' => '89e1fa42-ae1d-41dd-b591-97c06cc56150','type' => 'App\\Notifications\\LeaveApplied','notifiable_type' => 'App\\Components\\User\\Models\\User','notifiable_id' => '1','data' => '{"user_id":1,"leave_id":2}','read_at' => '2019-05-16 10:53:39','created_at' => '2019-05-16 10:51:36','updated_at' => '2019-05-16 10:53:39'],
          ['id' => '9a9864c3-ed65-4106-8c4b-781d00433c9a','type' => 'App\\Notifications\\TicketCreated','notifiable_type' => 'App\\Components\\User\\Models\\User','notifiable_id' => '2','data' => '{"created_by":1,"ticket_id":1}','read_at' => null,'created_at' => '2019-08-13 11:05:32','updated_at' => '2019-08-13 11:05:32'],
          ['id' => 'af63b7a0-c4c2-4cad-bb82-d4f9d8b6bd72','type' => 'App\\Notifications\\TicketCreated','notifiable_type' => 'App\\Components\\User\\Models\\User','notifiable_id' => '2','data' => '{"created_by":1,"ticket_id":2}','read_at' => null,'created_at' => '2019-08-13 11:08:35','updated_at' => '2019-08-13 11:08:35'],
          ['id' => 'e9feafd5-17ad-4209-bc23-5c09d33392c0','type' => 'App\\Notifications\\TicketCreated','notifiable_type' => 'App\\Components\\User\\Models\\User','notifiable_id' => '1','data' => '{"created_by":1,"ticket_id":2}','read_at' => null,'created_at' => '2019-08-13 11:08:35','updated_at' => '2019-08-13 11:08:35'],
          ['id' => 'f9426c07-d1ec-40e0-b85c-3c96f5d658f9','type' => 'App\\Notifications\\LeaveResponded','notifiable_type' => 'App\\Components\\User\\Models\\User','notifiable_id' => '1','data' => '{"leave_id":1,"admin":1}','read_at' => '2019-05-16 10:53:39','created_at' => '2019-05-16 10:51:43','updated_at' => '2019-05-16 10:53:39']
        ];

        DB::table('notifications')->insert($notifications);

        $notes = [
          ['id' => '1','notable_id' => '2','notable_type' => 'App\\Project','heading' => 'Where can I get some?','description' => '<p class="ql-align-justify">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don\'t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn\'t anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.</p><p><br></p>','created_by' => '1','created_at' => '2019-04-18 12:31:31','updated_at' => '2019-04-18 12:31:31'],
          ['id' => '2','notable_id' => '1','notable_type' => 'App\\Components\\User\\Models\\User','heading' => 'TEST employee note','description' => '<p>TEST</p>','created_by' => '1','created_at' => '2019-04-19 05:38:05','updated_at' => '2019-04-19 05:38:05'],
          ['id' => '3','notable_id' => '1','notable_type' => 'App\\Customer','heading' => 'Where does it come from?','description' => '<p class="ql-align-justify">Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32.</p><p class="ql-align-center"><br></p>','created_by' => '1','created_at' => '2019-04-19 06:04:01','updated_at' => '2019-04-19 06:46:13'],
          ['id' => '4','notable_id' => '2','notable_type' => 'App\\Customer','heading' => 'What is Lorem Ipsum?','description' => '<p><strong style="color: rgb(0, 0, 0);">Lorem Ipsum</strong><span style="color: rgb(0, 0, 0);">&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum</span></p>','created_by' => '1','created_at' => '2019-07-25 15:54:55','updated_at' => '2019-07-25 15:54:55']
        ];

        DB::table('notes')->insert($notes);

        $invoice_schemes = [
          ['id' => '1','name' => 'Custom','scheme_type' => 'custom','prefix' => 'custom','start_number' => '1','invoice_count' => '1','total_digits' => '5','is_default' => '1','created_at' => '2019-05-08 05:27:10','updated_at' => '2019-05-08 05:31:31'],
          ['id' => '2','name' => 'Year','scheme_type' => 'year','prefix' => null,'start_number' => '1','invoice_count' => '1','total_digits' => '5','is_default' => '0','created_at' => '2019-05-08 05:27:32','updated_at' => '2019-05-08 05:30:02']
        ];

        DB::table('invoice_schemes')->insert($invoice_schemes);
        
        $transactions = [
          ['id' => '1','project_id' => '1','ref_no' => 'custom-00001','category_id' => null,'expense_for' => null,'type' => 'invoice','status' => 'final','title' => 'Invoice forrecording song','customer_id' => '1','contact_id' => null,'invoice_scheme_id' => '1','transaction_date' => '2019-05-11','due_date' => '2019-05-30','discount_type' => 'fixed','discount_amount' => '1.000','total' => '25.250','terms' => 'Terms','notes' => 'Notes','payment_status' => 'paid','created_by' => '1','created_at' => '2019-05-10 05:00:44','updated_at' => '2019-05-20 12:22:11'],
          ['id' => '2','project_id' => '2','ref_no' => '2019-00001','category_id' => null,'expense_for' => null,'type' => 'invoice','status' => 'final','title' => 'Implemented Algorithem','customer_id' => '1','contact_id' => null,'invoice_scheme_id' => '2','transaction_date' => '2019-05-11','due_date' => '2019-05-11','discount_type' => 'fixed','discount_amount' => '2.000','total' => '263.200','terms' => 'Terms','notes' => 'Notes','payment_status' => 'partial','created_by' => '1','created_at' => '2019-05-10 05:02:01','updated_at' => '2019-05-20 12:22:28'],
          ['id' => '3','project_id' => '2','ref_no' => 'estimate1883092509','category_id' => null,'expense_for' => null,'type' => 'invoice','status' => 'estimate','title' => 'Desgin for game','customer_id' => '1','contact_id' => null,'invoice_scheme_id' => '2','transaction_date' => '2019-05-18','due_date' => '2019-05-15','discount_type' => 'fixed','discount_amount' => '2.000','total' => '110.000','terms' => 'Terms','notes' => 'Notes','payment_status' => 'due','created_by' => '1','created_at' => '2019-05-10 05:03:54','updated_at' => '2019-05-10 05:03:54'],
          ['id' => '4','project_id' => '2','ref_no' => '12365','category_id' => '21','expense_for' => '1','type' => 'expense','status' => 'final','title' => null,'customer_id' => null,'contact_id' => null,'invoice_scheme_id' => null,'transaction_date' => '2019-05-08','due_date' => '2019-05-09','discount_type' => 'fixed','discount_amount' => '0.000','total' => '562.000','terms' => null,'notes' => 'to be paid','payment_status' => 'due','created_by' => '1','created_at' => '2019-05-11 11:27:50','updated_at' => '2019-05-11 11:27:50'],
          ['id' => '5','project_id' => '1','ref_no' => '326','category_id' => '22','expense_for' => '1','type' => 'expense','status' => 'final','title' => null,'customer_id' => null,'contact_id' => null,'invoice_scheme_id' => null,'transaction_date' => '2019-05-14','due_date' => '2019-05-18','discount_type' => 'fixed','discount_amount' => '0.000','total' => '235.000','terms' => null,'notes' => 'not paid','payment_status' => 'due','created_by' => '1','created_at' => '2019-05-11 11:31:33','updated_at' => '2019-05-11 11:32:12']
        ];

        DB::table('transactions')->insert($transactions);

        $invoice_lines = [
            ['id' => '1','transaction_id' => '1','short_description' => 'Invoice forrecording song','long_description' => null,'rate' => '5.000','quantity' => '5.000','unit' => 'Hour','tax' => '5','total' => '25.000','created_at' => '2019-05-10 05:00:44','updated_at' => '2019-05-10 05:00:44'],
            ['id' => '2','transaction_id' => '2','short_description' => 'Implemented Algorithem','long_description' => null,'rate' => '52.000','quantity' => '5.000','unit' => 'Hour','tax' => '2','total' => '260.000','created_at' => '2019-05-10 05:02:01','updated_at' => '2019-05-10 05:02:01'],
            ['id' => '3','transaction_id' => '3','short_description' => 'Design for game','long_description' => null,'rate' => '56.000','quantity' => '2.000','unit' => 'Hour','tax' => '0','total' => '112.000','created_at' => '2019-05-10 05:03:54','updated_at' => '2019-05-10 05:03:54']
          ];

        DB::table('invoice_lines')->insert($invoice_lines);

        $transaction_payment_lines = [
          ['id' => '1','transaction_id' => '1','amount' => '25.250','conversion_rate' => '1.00','paid_on' => '2019-05-21','payment_details' => 'Paid','created_by' => '1','created_at' => '2019-05-20 12:22:11','updated_at' => '2019-05-20 12:22:11'],
          ['id' => '2','transaction_id' => '2','amount' => '23.200','conversion_rate' => '1.00','paid_on' => '2019-05-21','payment_details' => null,'created_by' => '1','created_at' => '2019-05-20 12:22:28','updated_at' => '2019-05-20 12:22:28']
        ];
        
        DB::table('transaction_payment_lines')->insert($transaction_payment_lines);
        
        $favorites = [
          ['id' => '1','user_id' => '1','favoritable_type' => 'App\\Project','favoritable_id' => '2','created_at' => '2019-04-18 12:29:28','updated_at' => '2019-04-18 12:29:28']
        ];

        DB::table('favorites')->insert($favorites);

        $categories = [
          ['id' => '1','name' => 'Designing','type' => 'projects','project_id' => null,'created_at' => '2019-04-18 12:26:57','updated_at' => '2019-04-18 12:26:57'],
          ['id' => '3','name' => 'Game Development for Android only','type' => 'projects','project_id' => null,'created_at' => '2019-04-19 07:13:33','updated_at' => '2019-04-19 07:13:33'],
          ['id' => '4','name' => 'Hospitality','type' => 'projects','project_id' => null,'created_at' => '2019-04-22 11:01:35','updated_at' => '2019-04-22 11:01:35'],
          ['id' => '18','name' => 'Internal Help','type' => 'knowledge_base','project_id' => null,'created_at' => '2019-04-25 05:40:11','updated_at' => '2019-04-25 05:40:11'],
          ['id' => '19','name' => 'Getting Started','type' => 'knowledge_base','project_id' => null,'created_at' => '2019-04-25 05:40:56','updated_at' => '2019-04-25 05:40:56'],
          ['id' => '20','name' => 'Algorithem','type' => 'tasks','project_id' => '2','created_at' => '2019-05-08 05:49:02','updated_at' => '2019-05-08 05:49:02'],
          ['id' => '21','name' => 'Advance Payment','type' => 'expenses','project_id' => null,'created_at' => '2019-05-11 11:27:19','updated_at' => '2019-05-11 11:27:19'],
          ['id' => '22','name' => 'Extra Expense','type' => 'expenses','project_id' => null,'created_at' => '2019-05-11 11:31:03','updated_at' => '2019-05-11 11:31:03'],
          ['id' => '23','name' => 'Support','type' => 'tickets','project_id' => null,'created_at' => '2019-08-13 11:03:56','updated_at' => '2019-08-13 11:03:56'],
          ['id' => '24','name' => 'Maintenance','type' => 'tickets','project_id' => null,'created_at' => '2019-08-13 11:08:19','updated_at' => '2019-08-13 11:08:19']
        ];


        DB::table('categories')->insert($categories);

        $knowledge_bases = [
        ['id' => '4','title' => 'What is Lorem Ipsum?','description' => '<p class="ql-align-justify"><strong>Lorem Ipsum</strong>&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p><p><br></p>','show_to_employee' => '1','is_active' => '1','created_by' => '1','created_at' => '2019-04-25 05:40:28','updated_at' => '2019-04-25 05:40:28'],
        ['id' => '5','title' => 'Why do we use it?','description' => '<p class="ql-align-justify">It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).</p><p><br></p>','show_to_employee' => '0','is_active' => '0','created_by' => '1','created_at' => '2019-04-25 05:41:15','updated_at' => '2019-04-25 05:41:15']
      ];

        DB::table('knowledge_bases')->insert($knowledge_bases);
      
        $categorables = [
          ['id' => '1','category_id' => '1','categorable_id' => '1','categorable_type' => 'App\\Project'],
          ['id' => '5','category_id' => '3','categorable_id' => '2','categorable_type' => 'App\\Project'],
          ['id' => '6','category_id' => '1','categorable_id' => '3','categorable_type' => 'App\\Project'],
          ['id' => '7','category_id' => '1','categorable_id' => '5','categorable_type' => 'App\\Project'],
          ['id' => '8','category_id' => '2','categorable_id' => '5','categorable_type' => 'App\\Project'],
          ['id' => '9','category_id' => '10','categorable_id' => '5','categorable_type' => 'App\\Project'],
          ['id' => '10','category_id' => '9','categorable_id' => '5','categorable_type' => 'App\\Project'],
          ['id' => '11','category_id' => '2','categorable_id' => '4','categorable_type' => 'App\\KnowladgeBase'],
          ['id' => '12','category_id' => '2','categorable_id' => '5','categorable_type' => 'App\\KnowladgeBase'],
          ['id' => '13','category_id' => '13','categorable_id' => '5','categorable_type' => 'App\\KnowladgeBase'],
          ['id' => '14','category_id' => '1','categorable_id' => '6','categorable_type' => 'App\\KnowladgeBase'],
          ['id' => '15','category_id' => '2','categorable_id' => '6','categorable_type' => 'App\\KnowladgeBase'],
          ['id' => '16','category_id' => '14','categorable_id' => '6','categorable_type' => 'App\\KnowladgeBase'],
          ['id' => '17','category_id' => '14','categorable_id' => '7','categorable_type' => 'App\\KnowladgeBase'],
          ['id' => '18','category_id' => '1','categorable_id' => '8','categorable_type' => 'App\\KnowladgeBase'],
          ['id' => '19','category_id' => '1','categorable_id' => '9','categorable_type' => 'App\\KnowladgeBase'],
          ['id' => '23','category_id' => '16','categorable_id' => '2','categorable_type' => 'App\\KnowledgeBase'],
          ['id' => '24','category_id' => '2','categorable_id' => '1','categorable_type' => 'App\\KnowledgeBase'],
          ['id' => '25','category_id' => '17','categorable_id' => '3','categorable_type' => 'App\\KnowledgeBase'],
          ['id' => '26','category_id' => '18','categorable_id' => '4','categorable_type' => 'App\\KnowledgeBase'],
          ['id' => '27','category_id' => '19','categorable_id' => '5','categorable_type' => 'App\\KnowledgeBase']
        ];

        DB::table('categorables')->insert($categorables);

        $tickets = [
          ['id' => '1','reference_no' => 'ticket#1','title' => 'Help adding discount.','category_id' => '23','priority' => 'high','description' => 'There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don\'t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn\'t anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc','status' => 'new','assigned_to' => '4','customer_id' => '1','created_by' => '1','updated_by' => '1','closed_at' => null,'created_at' => '2019-08-13 11:05:31','updated_at' => '2019-08-13 11:05:31'],
          ['id' => '2','reference_no' => 'ticket#2','title' => 'Help regarding app maintenance','category_id' => '24','priority' => 'medium','description' => 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source','status' => 'open','assigned_to' => '1','customer_id' => '1','created_by' => '1','updated_by' => '1','closed_at' => null,'created_at' => '2019-08-13 11:08:35','updated_at' => '2019-08-13 11:08:35']
        ];

        DB::table('tickets')->insert($tickets);
        
        $activity_log = [
          ['id' => '1','log_name' => 'default','description' => 'created','subject_id' => '1','subject_type' => 'App\\Components\\User\\Models\\User','causer_id' => null,'causer_type' => null,'properties' => '{"attributes":{"name":"Superadmin","email":"admin@example.com","mobile":null,"alternate_num":null,"home_address":null,"current_address":null,"address":null,"skype":null,"linkedin":null,"facebook":null,"twitter":null,"birth_date":null,"guardian_name":null,"gender":null,"note":null,"customer_id":null,"created_by":null,"password":"$2y$10$IaqWf4d8u4F\\/qD6JDOBI5exa\\/nDOQVJsETM0KzSKMhVopn04mAwBS","remember_token":"rIA3hNFYTV","created_at":"2019-04-18 12:03:19","updated_at":"2019-04-18 12:03:19","last_login":"2019-04-18 12:03:18","active":"2019-04-18 12:03:18","activation_key":"f25ea9af-9c92-4e3a-8a74-b6415e212ad9"}}','created_at' => '2019-04-18 12:03:19','updated_at' => '2019-04-18 12:03:19'],
          ['id' => '2','log_name' => 'default','description' => 'created','subject_id' => '1','subject_type' => 'App\\Customer','causer_id' => '1','causer_type' => 'App\\Components\\User\\Models\\User','properties' => '{"attributes":{"company":"Web Experts","tax_number":null,"mobile":"923232323","alternate_contact_no":null,"email":"webexpert@test.com","website":null,"city":null,"state":null,"zip_code":null,"country":null,"billing_address":null,"shipping_address":null,"currency_id":1,"created_by":1,"deleted_at":null,"created_at":"2019-04-18 12:20:13","updated_at":"2019-04-18 12:20:13"}}','created_at' => '2019-04-18 12:20:14','updated_at' => '2019-04-18 12:20:14'],
          ['id' => '3','log_name' => 'default','description' => 'updated','subject_id' => '1','subject_type' => 'App\\Customer','causer_id' => '1','causer_type' => 'App\\Components\\User\\Models\\User','properties' => '{"attributes":{"tax_number":"1010al","updated_at":"2019-04-18 12:20:33"},"old":{"tax_number":null,"updated_at":"2019-04-18 12:20:13"}}','created_at' => '2019-04-18 12:20:33','updated_at' => '2019-04-18 12:20:33'],
          ['id' => '4','log_name' => 'default','description' => 'updated','subject_id' => '1','subject_type' => 'App\\Customer','causer_id' => '1','causer_type' => 'App\\Components\\User\\Models\\User','properties' => '{"attributes":{"website":"https:\\/\\/www.example.com","updated_at":"2019-04-18 12:21:26"},"old":{"website":null,"updated_at":"2019-04-18 12:20:33"}}','created_at' => '2019-04-18 12:21:26','updated_at' => '2019-04-18 12:21:26'],
          ['id' => '5','log_name' => 'default','description' => 'updated','subject_id' => '1','subject_type' => 'App\\Customer','causer_id' => '1','causer_type' => 'App\\Components\\User\\Models\\User','properties' => '{"attributes":{"alternate_contact_no":"923232322","city":"Arizona","state":"Arizona","zip_code":"1010101","country":"USA","billing_address":"Linking Street, Arizona","shipping_address":"Linking Street, Arizona","updated_at":"2019-04-18 12:25:09"},"old":{"alternate_contact_no":null,"city":null,"state":null,"zip_code":null,"country":null,"billing_address":null,"shipping_address":null,"updated_at":"2019-04-18 12:21:26"}}','created_at' => '2019-04-18 12:25:09','updated_at' => '2019-04-18 12:25:09'],
          ['id' => '6','log_name' => 'default','description' => 'created','subject_id' => '2','subject_type' => 'App\\Components\\User\\Models\\User','causer_id' => '1','causer_type' => 'App\\Components\\User\\Models\\User','properties' => '{"attributes":{"name":"Mike","email":"mike@test.com","mobile":null,"alternate_num":null,"home_address":null,"current_address":null,"address":null,"skype":null,"linkedin":null,"facebook":null,"twitter":null,"birth_date":null,"guardian_name":null,"gender":null,"note":"Lorem ipsum dolor sit amet, consectetur adipiscing elit.","customer_id":"1","created_by":"1","password":"$2y$10$\\/NiMh9KLajJwRmRjuGUzo.4XqmQiNNrJWAtJR5PzuQXiQ4vV2F\\/ne","remember_token":null,"created_at":"2019-04-18 12:26:19","updated_at":"2019-04-18 12:26:19","last_login":null,"active":null,"activation_key":null}}','created_at' => '2019-04-18 12:26:19','updated_at' => '2019-04-18 12:26:19'],
          ['id' => '7','log_name' => 'default','description' => 'created','subject_id' => '1','subject_type' => 'App\\Project','causer_id' => '1','causer_type' => 'App\\Components\\User\\Models\\User','properties' => '{"attributes":{"name":"Musical Design","category_id":1,"customer_id":1,"billing_type":"fixed_rate","total_rate":"0.00","price_per_hours":"0.00","estimated_hours":"0.00","estimated_cost":"0.00","status":"not_started","lead_id":1,"start_date":"2019-04-18 00:00:00","end_date":"2019-06-08 00:00:00","description":"<p class=\\"ql-align-justify\\">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don\'t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn\'t anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.<\\/p><p><br><\\/p>","created_by":1,"favorite":0,"created_at":"2019-04-18 12:27:30","updated_at":"2019-04-18 12:27:30"}}','created_at' => '2019-04-18 12:27:30','updated_at' => '2019-04-18 12:27:30'],
          ['id' => '8','log_name' => 'default','description' => 'created','subject_id' => '2','subject_type' => 'App\\Project','causer_id' => '1','causer_type' => 'App\\Components\\User\\Models\\User','properties' => '{"attributes":{"name":"Zynga Game","category_id":2,"customer_id":1,"billing_type":"fixed_rate","total_rate":"0.00","price_per_hours":"0.00","estimated_hours":"0.00","estimated_cost":"0.00","status":"in_progress","lead_id":1,"start_date":"2019-04-18 00:00:00","end_date":"2019-06-15 00:00:00","description":"<p class=\\"ql-align-justify\\">The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from \\"de Finibus Bonorum et Malorum\\" by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.<\\/p><p><br><\\/p>","created_by":1,"favorite":0,"created_at":"2019-04-18 12:28:58","updated_at":"2019-04-18 12:28:58"}}','created_at' => '2019-04-18 12:28:58','updated_at' => '2019-04-18 12:28:58'],
          ['id' => '9','log_name' => 'default','description' => 'created','subject_id' => '1','subject_type' => 'App\\ProjectTask','causer_id' => '1','causer_type' => 'App\\Components\\User\\Models\\User','properties' => '{"attributes":{"project_id":2,"task_id":null,"subject":"Cras dictum libero eget erat rhoncus fermentum.","hourly_rate":"0.00","start_date":"2019-04-18 00:00:00","due_date":"2019-05-17 00:00:00","priority":"medium","description":"<p class=\\"ql-align-justify\\">The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from \\"de Finibus Bonorum et Malorum\\" by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.<\\/p><p><br><\\/p>","created_by":1,"show_to_customer":1,"is_completed":0,"created_at":"2019-04-18 12:30:49","updated_at":"2019-04-18 12:30:49"}}','created_at' => '2019-04-18 12:30:49','updated_at' => '2019-04-18 12:30:49'],
          ['id' => '10','log_name' => 'default','description' => 'created','subject_id' => '1','subject_type' => 'App\\Note','causer_id' => '1','causer_type' => 'App\\Components\\User\\Models\\User','properties' => '{"attributes":{"notable_id":2,"notable_type":"App\\\\Project","heading":"Where can I get some?","description":"<p class=\\"ql-align-justify\\">There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don\'t look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn\'t anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.<\\/p><p><br><\\/p>","created_by":1,"created_at":"2019-04-18 12:31:31","updated_at":"2019-04-18 12:31:31"}}','created_at' => '2019-04-18 12:31:31','updated_at' => '2019-04-18 12:31:31'],
          ['id' => '11','log_name' => 'default','description' => 'updated','subject_id' => '1','subject_type' => 'App\\ProjectTask','causer_id' => '1','causer_type' => 'App\\Components\\User\\Models\\User','properties' => '{"attributes":{"is_completed":1,"updated_at":"2019-04-18 12:31:52"},"old":{"is_completed":0,"updated_at":"2019-04-18 12:30:49"}}','created_at' => '2019-04-18 12:31:52','updated_at' => '2019-04-18 12:31:52'],
          ['id' => '12','log_name' => 'default','description' => 'updated','subject_id' => '1','subject_type' => 'App\\ProjectTask','causer_id' => '1','causer_type' => 'App\\Components\\User\\Models\\User','properties' => '{"attributes":{"is_completed":0,"updated_at":"2019-04-18 12:32:05"},"old":{"is_completed":1,"updated_at":"2019-04-18 12:31:52"}}','created_at' => '2019-04-18 12:32:05','updated_at' => '2019-04-18 12:32:05'],
          ['id' => '14','log_name' => 'default','description' => 'updated','subject_id' => '1','subject_type' => 'App\\Components\\User\\Models\\User','causer_id' => '1','causer_type' => 'App\\Components\\User\\Models\\User','properties' => '{"attributes":{"mobile":"900909090","alternate_num":"10190190","skype":"sky","linkedin":"www.linkedin.com","facebook":"www.fb.com","twitter":"www.twitter.com","birth_date":"1985-01-01","guardian_name":"Test","gender":"male","updated_at":"2019-04-18 12:35:11"},"old":{"mobile":null,"alternate_num":null,"skype":null,"linkedin":null,"facebook":null,"twitter":null,"birth_date":null,"guardian_name":null,"gender":null,"updated_at":"2019-04-18 12:03:19"}}','created_at' => '2019-04-18 12:35:11','updated_at' => '2019-04-18 12:35:11'],
          ['id' => '15','log_name' => 'default','description' => 'created','subject_id' => '2','subject_type' => 'App\\ProjectTask','causer_id' => '1','causer_type' => 'App\\Components\\User\\Models\\User','properties' => '{"attributes":{"project_id":1,"task_id":null,"subject":"Song recording","hourly_rate":"0.00","start_date":"2019-04-18 00:00:00","due_date":"2019-04-20 00:00:00","priority":"high","description":null,"created_by":1,"show_to_customer":0,"is_completed":0,"created_at":"2019-04-18 12:36:07","updated_at":"2019-04-18 12:36:07"}}','created_at' => '2019-04-18 12:36:07','updated_at' => '2019-04-18 12:36:07'],
          ['id' => '19','log_name' => 'default','description' => 'created','subject_id' => '3','subject_type' => 'App\\ProjectTask','causer_id' => '1','causer_type' => 'App\\Components\\User\\Models\\User','properties' => '{"attributes":{"project_id":2,"task_id":null,"category_id":null,"subject":"implementing algorithem for game","hourly_rate":"0.00","start_date":"2019-05-16 00:00:00","due_date":"2019-05-15 00:00:00","priority":"medium","description":"<h1 class=\\"ql-align-justify\\">Minimax Algorithm in Game Theory | Set 1 (Introduction)<\\/h1><p class=\\"ql-align-justify\\"><br><\\/p><p>Minimax is a kind of&nbsp;<a href=\\"https:\\/\\/www.geeksforgeeks.org\\/tag\\/backtracking\\/\\" target=\\"_blank\\" style=\\"color: rgb(236, 78, 32);\\">backtracking<\\/a>&nbsp;algorithm that is used in decision making and game theory to find the optimal move for a player, assuming that your opponent also plays optimally. It is widely used in two player turn-based games such as Tic-Tac-Toe, Backgammon, Mancala, Chess, etc.<\\/p><p>In Minimax the two players are called maximizer and minimizer. The&nbsp;<strong>maximizer<\\/strong>&nbsp;tries to get the highest score possible while the&nbsp;<strong>minimizer<\\/strong>&nbsp;tries to do the opposite and get the lowest score possible.<\\/p><p class=\\"ql-align-justify\\">Every board state has a value associated with it. In a given state if the maximizer has upper hand then, the score of the board will tend to be some positive value. If the minimizer has the upper hand in that board state then it will tend to be some negative value. The values of the board are calculated by some heuristics which are unique for every type of game.<\\/p><p class=\\"ql-align-justify\\"><br><\\/p><p><a href=\\"https:\\/\\/www.geeksforgeeks.org\\/minimax-algorithm-in-game-theory-set-1-introduction\\/\\" target=\\"_blank\\"><strong><em>to read more...<\\/em><\\/strong><\\/a><\\/p>","created_by":1,"show_to_customer":0,"is_completed":0,"created_at":"2019-05-08 05:49:06","updated_at":"2019-05-08 05:49:06"}}','created_at' => '2019-05-08 05:49:06','updated_at' => '2019-05-08 05:49:06'],
          ['id' => '20','log_name' => 'default','description' => 'created','subject_id' => '1','subject_type' => 'App\\Transaction','causer_id' => '1','causer_type' => 'App\\Components\\User\\Models\\User','properties' => '{"attributes":{"project_id":1,"type":"invoice","status":"final","ref_no":"custom-00002","title":"Invoice forrecording song","customer_id":1,"contact_id":null,"invoice_scheme_id":1,"transaction_date":"2019-05-11","due_date":"2019-05-30","discount_type":"fixed","discount_amount":"1.000","total":"25.250","terms":"Terms","notes":"Notes","payment_status":"due","created_by":1,"created_at":"2019-05-10 05:00:44","updated_at":"2019-05-10 05:00:44"}}','created_at' => '2019-05-10 05:00:44','updated_at' => '2019-05-10 05:00:44'],
          ['id' => '21','log_name' => 'default','description' => 'created','subject_id' => '2','subject_type' => 'App\\Transaction','causer_id' => '1','causer_type' => 'App\\Components\\User\\Models\\User','properties' => '{"attributes":{"project_id":2,"type":"invoice","status":"final","ref_no":"2019-00002","title":"Implemented Algorithem","customer_id":1,"contact_id":null,"invoice_scheme_id":2,"transaction_date":"2019-05-11","due_date":"2019-05-11","discount_type":"fixed","discount_amount":"2.000","total":"263.200","terms":"Terms","notes":"Notes","payment_status":"due","created_by":1,"created_at":"2019-05-10 05:02:01","updated_at":"2019-05-10 05:02:01"}}','created_at' => '2019-05-10 05:02:01','updated_at' => '2019-05-10 05:02:01'],
          ['id' => '22','log_name' => 'default','description' => 'created','subject_id' => '3','subject_type' => 'App\\Transaction','causer_id' => '1','causer_type' => 'App\\Components\\User\\Models\\User','properties' => '{"attributes":{"project_id":2,"type":"invoice","status":"estimate","ref_no":"estimate1883092509","title":"Desgin for game","customer_id":1,"contact_id":null,"invoice_scheme_id":2,"transaction_date":"2019-05-18","due_date":"2019-05-15","discount_type":"fixed","discount_amount":"2.000","total":"110.000","terms":"Terms","notes":"Notes","payment_status":"due","created_by":1,"created_at":"2019-05-10 05:03:54","updated_at":"2019-05-10 05:03:54"}}','created_at' => '2019-05-10 05:03:54','updated_at' => '2019-05-10 05:03:54'],
          ['id' => '23','log_name' => 'default','description' => 'created','subject_id' => '2','subject_type' => 'App\\Customer','causer_id' => '1','causer_type' => 'App\\Components\\User\\Models\\User','properties' => '{"attributes":{"company":"Digital Ways","currency_id":1,"tax_number":null,"mobile":"7890130490","alternate_contact_no":null,"email":"digitalways@example.com","website":null,"city":null,"state":null,"zip_code":null,"country":null,"billing_address":null,"shipping_address":null,"status_id":2,"source_id":1,"assigned_to":1,"contacted_date":"2019-07-26 13:25:00","description":null,"created_by":1,"deleted_at":null,"created_at":"2019-07-25 15:50:54","updated_at":"2019-07-25 15:50:54"}}','created_at' => '2019-07-25 15:50:54','updated_at' => '2019-07-25 15:50:54'],
          ['id' => '24','log_name' => 'default','description' => 'created','subject_id' => '3','subject_type' => 'App\\Components\\User\\Models\\User','causer_id' => '1','causer_type' => 'App\\Components\\User\\Models\\User','properties' => '{"attributes":{"name":"John Tyson","email":"digitalways@example.com","mobile":null,"alternate_num":null,"home_address":null,"current_address":null,"address":null,"skype":null,"linkedin":null,"facebook":null,"twitter":null,"birth_date":null,"guardian_name":null,"gender":null,"account_holder_name":null,"account_no":null,"bank_name":null,"bank_identifier_code":null,"branch_location":null,"tax_payer_id":null,"note":null,"password":null,"created_by":null,"customer_id":"2","last_login":null,"active":null,"activation_key":null,"remember_token":null,"created_at":"2019-07-25 15:50:55","updated_at":"2019-07-25 15:50:55"}}','created_at' => '2019-07-25 15:50:55','updated_at' => '2019-07-25 15:50:55'],
          ['id' => '25','log_name' => 'default','description' => 'created','subject_id' => '4','subject_type' => 'App\\Note','causer_id' => '1','causer_type' => 'App\\Components\\User\\Models\\User','properties' => '{"attributes":{"notable_id":2,"notable_type":"App\\\\Customer","heading":"What is Lorem Ipsum?","description":"<p><strong style=\\"color: rgb(0, 0, 0);\\">Lorem Ipsum<\\/strong><span style=\\"color: rgb(0, 0, 0);\\">&nbsp;is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum<\\/span><\\/p>","created_by":1,"created_at":"2019-07-25 15:54:55","updated_at":"2019-07-25 15:54:55"}}','created_at' => '2019-07-25 15:54:55','updated_at' => '2019-07-25 15:54:55']
        ];

        DB::table('activity_log')->insert($activity_log);

        Artisan::call('permission:cache-reset');
    }
}
