<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateSettingRequest;
use App\Models\Setting;
use App\Models\Status;
use App\Repositories\SettingRepository;
use Flash;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\View\View;

class SettingController extends AppBaseController
{
    /** @var SettingRepository */
    private $settingRepository;

    public function __construct(SettingRepository $settingRepo)
    {
        $this->settingRepository = $settingRepo;
    }

    /**
     * Show the form for editing the specified Setting.
     *
     * @param  Request  $request
     * @return Factory|View
     */
    public function edit(Request $request)
    {
        $groupName = $request->get('group', 'general');
        $settings = $this->settingRepository->getSyncList($groupName);
        $invoiceTemplateArray = Setting::INVOICE__TEMPLATE_ARRAY;
        asort($invoiceTemplateArray);
        $composerFile = file_get_contents(base_path('composer.json'));
        $composerData = json_decode($composerFile, true);
        $currentVersion = $composerData['version'];
        $taskStatus = Status::get();

        return view('settings.edit', compact('settings', 'groupName', 'invoiceTemplateArray', 'currentVersion', 'taskStatus'));
    }

    /**
     * Update the specified Setting in storage.
     *
     * @param  UpdateSettingRequest  $request
     * @return RedirectResponse|Redirector
     */
    public function update(UpdateSettingRequest $request)
    {
        $this->settingRepository->updateSetting($request->all());

        Flash::success('Setting updated successfully.');

        return redirect(route('settings.edit'));
    }

    /**
     * @param  Request  $request
     * @return RedirectResponse|Redirector
     */
    public function invoiceSettingUpdate(Request $request)
    {
        $this->settingRepository->updateInvoiceSetting($request->all());

        Flash::success('Invoice template updated successfully');

        return redirect('settings?group=invoice_template');
    }

    /**
     * @param  Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|RedirectResponse|Redirector
     */
    public function googleReCaptchaUpdate(Request $request)
    {
        
        try {
            $this->settingRepository->updateGoogleRecaptchaSetting($request->all());
            Flash::success('Google Recaptcha updated successfully');
        } catch (\Exception $exception) {
            Flash::error($exception->getMessage());
        }

        return redirect('settings?group=google_recaptcha');
    }
}
