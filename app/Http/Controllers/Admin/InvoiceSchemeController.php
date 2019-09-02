<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\InvoiceScheme;
use Illuminate\Http\Request;

class InvoiceSchemeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!request()->user()->can('sales.invoices')) {
            abort(403, 'Unauthorized action.');
        }

        $rowsPerPage = (request()->get('rowsPerPage') > 0) ? request()->get('rowsPerPage') : 0;
        $sort_by = request()->get('sort_by');
        $descending = request()->get('descending');

        if ('false' == $descending) {
            $orderby = 'asc';
        } elseif ('true' == $descending) {
            $orderby = 'desc';
        } elseif ('' == $descending) {
            $orderby = 'desc';
            $sort_by = 'id';
        }

        $invoice_schemes = InvoiceScheme::orderBy($sort_by, $orderby)
                        ->paginate($rowsPerPage);

        return $invoice_schemes;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!request()->user()->can('sales.invoices')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = $request->only('name', 'scheme_type', 'prefix', 'start_number', 'total_digits');
            $input['is_default'] = !empty($request->input('is_default')) ? 1 : 0;

            //if is_default is 1 update previous to 0
            if ($input['is_default'] == 1) {
                InvoiceScheme::where('is_default', 1)
                            ->update(['is_default' => 0]);
            }
            
            $invoice_scheme = InvoiceScheme::create($input);

            $output = $this->respondSuccess(__('messages.saved_successfully'), ['invoice_scheme' => $invoice_scheme]);
        } catch (Exception $e) {
            $output = $this->respondWentWrong($e);
        }

        return $output;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\InvoiceScheme  $invoiceScheme
     * @return \Illuminate\Http\Response
     */
    public function show(InvoiceScheme $invoiceScheme)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\InvoiceScheme  $invoiceScheme
     * @return \Illuminate\Http\Response
     */
    public function edit(InvoiceScheme $invoiceScheme)
    {
        if (!request()->user()->can('sales.invoices')) {
            abort(403, 'Unauthorized action.');
        }

        unset($invoiceScheme['created_at'], $invoiceScheme['updated_at']);
        
        return $this->respond($invoiceScheme);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\InvoiceScheme  $invoiceScheme
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        if (!request()->user()->can('sales.invoices')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $input = request()->only('name', 'scheme_type', 'prefix', 'start_number', 'total_digits');

            $input['is_default'] = !empty(request()->input('is_default')) ? 1 : 0;
            
            //if is_default is 1 update previous to 0
            if ($input['is_default'] == 1) {
                InvoiceScheme::where('is_default', 1)
                            ->update(['is_default' => 0]);
            }

            $invoice_scheme = InvoiceScheme::where('id', $id)->update($input);
            
            $output = $this->respondSuccess(__('messages.updated_successfully'));
        } catch (Exception $e) {
            $output = $this->respondWentWrong($e);
        }
        return $output;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\InvoiceScheme  $invoiceScheme
     * @return \Illuminate\Http\Response
     */
    public function destroy(InvoiceScheme $invoiceScheme)
    {
        if (!request()->user()->can('sales.invoices')) {
            abort(403, 'Unauthorized action.');
        }
        
        try {
            $invoiceScheme->delete();

            $output = $this->respondSuccess(__('messages.deleted_successfully'));
        } catch (Exception $e) {
            $output = $this->respondWentWrong($e);
        }
        return $output;
    }
}
