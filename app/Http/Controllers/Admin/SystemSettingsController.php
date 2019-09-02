<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Util\CommonUtil;
use App\System;
use DateTimeZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SystemSettingsController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $commonUtil;
    protected $emailTemplates;

    /**
     * Constructor.
     *
     * @param CommonUtil
     */
    public function __construct(CommonUtil $commonUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->emailTemplates = [
            'employee_added_template' => [
                'tags' => '{employee_name}, {email}, {password}, {login_link}, {company_name}',
                'type' => 'employee'
            ],
            'send_reminder_notification_template' => [
                'tags' => '{reminder_for}, {remind_to}, {remind_on}, {notes}, {company_name}',
                'type' => 'employee'
            ],


            'customer_added_template' => [
                'tags' => '{customer_name}, {company_name}',
                'type' => 'customer'
            ],
            'customers_contact_added_template' => [
                'tags' => '{contact_name}, {customer_name}, {email}, {password}, {login_link}, {company_name}',
                'type' => 'customer'
            ],
            'send_invoice_to_customer_template' => [
                'tags' => '{invoice_number}, {customer_name}, {due_date}, {company_name}',
                'type' => 'customer',
                'add_attachment' => true
            ],
            'invoice_reminder_template' =>
            [
                'tags' => '{invoice_number}, {project_name}, {customer_name}, {due_date}, {company_name}',
                'type' => 'customer',
                'add_attachment' => true
            ],
            
            'lead_added_template' => [
                'tags' => '{lead_name}, {company_name}',
                'type' => 'leads'
            ],
            
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!request()->user()->can('setting')) {
            abort(403, 'Unauthorized action.');
        }

        $system = System::pluck('value', 'key')
                        ->toArray();

        $email_templates = [];
        foreach ($this->emailTemplates as $key => $val) {
            $template_values = !empty($system[$key]) ? json_decode($system[$key], true) : [];

            $email_templates[$key] = [
                'key' => $key,
                'name' => __('messages.' . $key),
                'subject' => !empty($template_values['subject']) ? $template_values['subject'] : '',
                'body' => !empty($template_values['body']) ? $template_values['body'] : '',
                'attachment' => !empty($template_values['attachment']) ? '1' : '0',
                'tags' => $val['tags'],
                'type' => $val['type'],
                'add_attachment' => !empty($val['add_attachment']) ? 1 : 0
            ];
        }

        $data = ['system' => $system];
        $data['email_templates'] = $email_templates;
        
        if (!empty($system['logo'])) {
            $logo = Storage::url($system['logo']);
            $data['logo'] = $logo;
        }

        if (!empty($system['favicon'])) {
            $favicon = Storage::url($system['favicon']);
            $data['favicon'] = $favicon;
        }

        $date_formats = System::date_formats();
        $time_formats = System::time_formats();
        //timezones
        $timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        $timezone_list = [];
        foreach ($timezones as $timezone) {
            $timezone_list[] =  ['text' => $timezone, 'value' => $timezone];
        }
        //languages
        $config_languages = config('constants.langs');
        $languages = [];
        foreach ($config_languages as $key => $value) {
            $languages[] =  ['text' => $value['full_name'], 'value' => $key];
        }

        $default_values = [
                'APP_NAME' => env('APP_NAME'),
                'APP_TITLE' => env('APP_TITLE'),
                'APP_LOCALE' => env('APP_LOCALE'),
                'APP_TIMEZONE' => env('APP_TIMEZONE'),
                'ENABLE_CLIENT_SIGNUP' => empty(env('ENABLE_CLIENT_SIGNUP')) ? '0' : '1'
            ];

        $week_days = [
            ['text' => __('messages.sunday'), 'value' => '0'],
            ['text' => __('messages.monday'), 'value' => '1'],
            ['text' => __('messages.tuesday'), 'value' => '2'],
            ['text' => __('messages.wednesday'), 'value' => '3'],
            ['text' => __('messages.thursday'), 'value' => '4'],
            ['text' => __('messages.friday'), 'value' => '5'],
            ['text' => __('messages.saturday'), 'value' => '6'],
        ];

        $data['default_values'] = $default_values;
        $data['time_formats'] = $time_formats;
        $data['date_formats'] = $date_formats;
        $data['languages'] = $languages;
        $data['timezone_list'] = $timezone_list;
        $data['week_days'] = $week_days;
        $data['cron_job_command'] = $this->commonUtil->getCronJobCommand();

        return $data;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!request()->user()->can('setting')) {
            abort(403, 'Unauthorized action.');
        }
        
        try {
            if ($this->isDemo()) {
                return $this->respondDemo();
            }
            
            $inputs = $request->only(
                'tax_number',
                'mobile',
                'alternate_contact_no',
                'email',
                'city',
                'state',
                'country',
                'zip_code',
                'logo',
                'date_format',
                'time_format',
                'currency_id',
                'first_day_of_week',
                'address_line_1',
                'address_line_2',
                'favicon'
            );

            $email_templates = $request->input('email_templates');

            foreach ($email_templates as $key => $value) {
                $inputs[$key] = json_encode([
                    'subject' => $value['subject'],
                    'body' => $value['body'],
                    'attachment' => !empty($value['attachment']) ? 1 : 0,
                ]);
            }

            $env_settings = $request->only('APP_NAME', 'APP_TITLE', 'APP_LOCALE', 'APP_TIMEZONE', 'ENABLE_CLIENT_SIGNUP');

            $env_settings['ENABLE_CLIENT_SIGNUP'] = empty($env_settings['ENABLE_CLIENT_SIGNUP']) ? false : true;

            foreach ($inputs as $key => $value) {
                System::updateOrCreate(['key' => $key], ['value' => $value]);
            }

            $found_envs = [];
            $env_path = base_path('.env');
            $env_lines = file($env_path);
            foreach ($env_settings as $index => $value) {
                foreach ($env_lines as $key => $line) {
                    //Check if present then replace it.
                    if (strpos($line, $index) !== false) {
                        $env_lines[$key] = is_string($value) ? $index . '="' . $value . '"' . PHP_EOL : "$index=$value" . PHP_EOL;

                        $found_envs[] = $index;
                    }
                }
            }

            //Add the missing env settings
            $missing_envs = array_diff(array_keys($env_settings), $found_envs);
            if (!empty($missing_envs)) {
                $missing_envs = array_values($missing_envs);
                foreach ($missing_envs as $k => $key) {
                    if ($k == 0) {
                        $env_lines[] = is_string($env_settings[$key]) ? PHP_EOL . $key . '="' . $env_settings[$key] . '"' . PHP_EOL : PHP_EOL . "$key=$env_settings[$key]". PHP_EOL;
                    } else {
                        $env_lines[] = is_string($env_settings[$key]) ? $key . '="' . $env_settings[$key] . '"' . PHP_EOL : "$key=$env_settings[$key]" . PHP_EOL;
                    }
                }
            }

            $env_content = implode('', $env_lines);

            if (is_writable($env_path) && file_put_contents($env_path, $env_content)) {
                $output = $this->respondSuccess(__('messages.saved_successfully'));
            } else {
                $output = $this->respondWithError(__('messages.env_permission'));
            }
        } catch (Exception $e) {
            $output = $this->respondWentWrong($e);
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Upload logo.
     *
     * @param  \Illuminate\Http\Response
     * @return $path
     */
    public function uploadLogo(Request $request)
    {
        $type = $request->get('type');
        $file = $request->file('file');

        $system = System::pluck('value', 'key')
                        ->toArray();

        //Generate a unique name for the file.
        $logo_name = time() . '_' . $file->getClientOriginalName();

        $path = $file->storeAs($type, $logo_name);

        if (!empty($system['logo'])) {
            Storage::delete($system['logo']);
        }

        return $path;
    }
}
