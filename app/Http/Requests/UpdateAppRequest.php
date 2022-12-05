<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $id = $this->route('app') ? $this->route('app')->id : null;
        return [
            'uid' => ['string' , 'required' , 'max:255' , 'unique:apps,uid,'.$id],
            'name' => ['string' , 'required' , 'max:255' ],
            'platform_id' => ['required' , 'exists:platforms,id' ]
        ];
    }
}
