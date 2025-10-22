<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Theme;
use App\Models\Design;

class UpdateDesignRequest extends FormRequest
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
     * Validation rules for updating a design.
     */
    public function rules(): array
    {
        return [
            'theme_id' => 'sometimes|required|exists:themes,id',
            'name' => 'sometimes|required|max:255',
            'primary_color' => 'sometimes|required|max:50',
            'secondary_color' => 'sometimes|required|max:50',
            'tertiary_color' => 'nullable|max:50',
            'bg_images' => 'required|array|min:1',
            'bg_images.*' => 'file|mimes:png,webp|max:10240|dimensions:min_width=540,min_height=1080',
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * If theme_id is changed, regenerate the code based on new theme.
     */
    protected function prepareForValidation(): void
    {
        // Get the Design being updated (from route model binding or route parameter)
        $design = $this->route('design');

        // Only regenerate code if theme_id is being updated
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

                // If theme_id changed, regenerate new code
                if ($design && $design->theme_id != $this->theme_id) {
                    $this->merge([
                        'code' => $code,
                    ]);
                }
            }
        }
    }
}
