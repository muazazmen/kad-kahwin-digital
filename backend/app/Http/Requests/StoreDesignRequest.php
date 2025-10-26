<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Theme;
use App\Models\Design;

class StoreDesignRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Allow all authenticated users (adjust if you need roles)
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'code' => 'nullable|string|max:100',
            'theme_id' => 'required|exists:themes,id',
            'name' => 'required|max:255',
            'primary_color' => 'required|max:50',
            'secondary_color' => 'required|max:50',
            'tertiary_color' => 'nullable|max:50',
            'thumbnails' => 'nullable|array|min:1',
            'thumbnails.*' => 'file|mimes:png,webp|max:10240|dimensions:min_width=540,min_height=1080',
            'bg_images' => 'required|array|min:1',
            'bg_images.*' => 'file|mimes:png,webp|max:10240|dimensions:min_width=540,min_height=1080',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * This is where we auto-generate the `code` based on theme name.
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled('theme_id')) {
            $theme = Theme::find($this->theme_id);

            if ($theme) {
                $prefix = strtoupper(substr($theme->name, 0, 3));

                $latest = Design::where('code', 'like', "{$prefix}%")
                    ->orderBy('code', 'desc')
                    ->first();

                $nextNumber = $latest
                    ? ((int) substr($latest->code, 3)) + 1
                    : 1;

                $code = sprintf('%s%04d', $prefix, $nextNumber);

                // merge new 'code' field into request data
                $this->merge([
                    'code' => $code,
                ]);
            }
        }
    }
}
