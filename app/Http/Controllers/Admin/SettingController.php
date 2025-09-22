<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\StoreSettingRequest;
use App\Http\Requests\UpdateSettingRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\MassDestroySettingRequest;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class SettingController extends Controller
{
    use MediaUploadingTrait;

    public function index()
    {
        $setting = Setting::first();
        abort_if(Gate::denies('setting_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');


        return view('admin.settings.edit', compact('setting'));
    }


    public function update(UpdateSettingRequest $request)
    {
        // dd($request->all());
        $setting = Setting::first();

        $setting->update($request->all());

        if ($logo = $request->file('logo')) {
            if ($setting->logo) {
                $setting->logo->delete();
            }
            $logo = $this->manualStoreMedia($logo)['name'];
            $setting->addMedia(storage_path('tmp/uploads/' . basename($logo)))->toMediaCollection('logo');
        }

        if ($favicon = $request->file('favicon')) {
            if ($setting->favicon) {
                $setting->favicon->delete();
            }
            $favicon = $this->manualStoreMedia($favicon)['name'];
            $setting->addMedia(storage_path('tmp/uploads/' . basename($favicon)))->toMediaCollection('favicon');
        }

        return redirect()->route('admin.settings.index')->with('success', 'Settings Updated successfully');
    }

}
