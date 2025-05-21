<?php
/**
 * Company: InfyOm Technologies, Copyright 2019, All Rights Reserved.
 *
 * User: Vishal Ribdiya
 * Email: vishal.ribdiya@infyom.com
 * Date: 6/15/2019
 * Time: 1:01 PM
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules['name'] = 'required|max:250|unique:status,name,'.$this->route('status')->id;
        $rules['order'] = 'integer|min:0|unique:status,order,'.$this->route('status')->id;

        return $rules;
    }

    public function messages()
    {
        return [
            'order.min' => 'The order must be in positive value.',
            'order.integer' => 'The order must be an number.',

        ];
    }
}
