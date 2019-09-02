<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Storage;

class BackupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!request()->user()->can('backup')) {
            abort(403, 'Unauthorized action.');
        }

        $disk = Storage::disk(config('backup.backup.destination.disks')[0]);

        $files = $disk->files(config('backup.backup.name'));

        $backups = [];
        // make an array of backup files, with their filesize and creation date
        foreach ($files as $k => $f) {
            // only take the zip files into account
            if (substr($f, -4) == '.zip' && $disk->exists($f)) {
                $file_name = str_replace(config('backup.backup.name') . '/', '', $f);
                $backups[] = [
                    'file_path' => $f,
                    'file_name' => $file_name,
                    'file_size' => $disk->size($f),
                    'last_modified' => $disk->lastModified($f),
                    'download_link' => action('Admin\BackupController@download', [$file_name]),
                ];
            }
        }

        // reverse the backups, so the newest one would be on top
        $backups = array_reverse($backups);

        return $backups;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!request()->user()->can('backup')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if ($this->isDemo()) {
                return $this->respondDemo();
            }
            
            // start the backup process
            Artisan::call('backup:run');

            $output = $this->respondSuccess(__('messages.backed_up_successfully'));
        } catch (Exception $e) {
            $output = $this->respondWentWrong($e);
        }
        
        return $output;
    }

    /**
     * Downloads a backup zip file.
     *
     * @param  int  $file_name
     * @return \Illuminate\Http\Response
     */
    public function download($file_name)
    {
        if (!request()->user()->can('backup')) {
            abort(403, 'Unauthorized action.');
        }

        $file = config('backup.backup.name') . '/' . $file_name;
        $disk = Storage::disk(config('backup.backup.destination.disks')[0]);
        if ($disk->exists($file)) {
            $fs = Storage::disk(config('backup.backup.destination.disks')[0])->getDriver();
            $stream = $fs->readStream($file);
            return \Response::stream(function () use ($stream) {
                fpassthru($stream);
            }, 200, [
                "Content-Type" => $fs->getMimetype($file),
                "Content-Length" => $fs->getSize($file),
                "Content-disposition" => "attachment; filename=\"" . basename($file) . "\"",
            ]);
        }
    }

    /**
     * Deletes a backup file.
     *
     * @param  int  $file_name
     * @return \Illuminate\Http\Response
     */
    public function delete($file_name)
    {
        if (!request()->user()->can('backup')) {
            abort(403, 'Unauthorized action.');
        }
        
        try {
            $disk = Storage::disk(config('backup.backup.destination.disks')[0]);

            if ($disk->exists(config('backup.backup.name') . '/' . $file_name)) {
                $disk->delete(config('backup.backup.name') . '/' . $file_name);
            }

            $output = $this->respondSuccess(__('messages.deleted_successfully'));
        } catch (Exception $e) {
            $output = $this->respondWentWrong($e);
        }
        return $output;
    }
}
