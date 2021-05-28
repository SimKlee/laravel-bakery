<?php declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class AbstractModelStoreRequest
 * @package App\Http\Requests
 */
abstract class AbstractModelStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    abstract public function rules(): array;
}
