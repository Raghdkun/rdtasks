<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTaxRequest;
use App\Http\Requests\UpdateTaxRequest;
use App\Models\Invoice;
use App\Models\Tax;
use App\Repositories\TaxRepository;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaxController extends AppBaseController
{
    /** @var TaxRepository */
    private $taxRepository;

    public function __construct(TaxRepository $taxRepo)
    {
        $this->taxRepository = $taxRepo;
    }

    /**
     * Display a listing of the Tax.
     *
     * @param  Request  $request
     * @return Application|Factory|View
     *
     * @throws Exception
     */
    public function index(Request $request)
    {
        return view('taxes.index');
    }

    /**
     * Store a newly created Tax in storage.
     *
     * @param  CreateTaxRequest  $request
     * @return JsonResponse
     */
    public function store(CreateTaxRequest $request)
    {
        $input = $request->all();

        $data['tax'] = $this->taxRepository->create($input);

        $taxes = Tax::orderBy('tax', 'asc')->get();
        $data['taxes'] = [];
        foreach ($taxes as $tax) {
            $data['taxes'][$tax->name.' - '.'{'.$tax->tax.' %}'] = $tax->id;
        }

        $data['taxesArr'] = $taxes->pluck('tax', 'id')->toArray();

        return $this->sendResponse($data, 'Tax saved successfully.');
    }

    /**
     * Show the form for editing the specified Tax.
     *
     * @param  Tax  $tax
     * @return JsonResponse
     */
    public function edit(Tax $tax)
    {
        return $this->sendResponse($tax, 'Tax retrieved successfully.');
    }

    /**
     * Update the specified Tax in storage.
     *
     * @param  Tax  $tax
     * @param  UpdateTaxRequest  $request
     * @return JsonResponse
     */
    public function update(Tax $tax, UpdateTaxRequest $request)
    {
        $input = $request->all();

        $tax = $this->taxRepository->update($input, $tax->id);

        return $this->sendSuccess('Tax updated successfully.');
    }

    /**
     * Remove the specified Tax from storage.
     *
     * @param  Tax  $tax
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function destroy(Tax $tax)
    {
        $invoice = Invoice::where('tax_id', $tax->id)->first();
        if ($invoice) {
            return $this->sendError('Can not delete.  This tax is used in invoice. ');
        }
        $tax->delete();

        return $this->sendSuccess('Tax deleted successfully.');
    }
}
