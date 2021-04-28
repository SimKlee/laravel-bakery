<?php declare(strict_types=1);

namespace SimKlee\LaravelBakery\Support;

use Illuminate\Support\Str;

/**
 * Class AbstractBag
 * @package SimKlees\LaravelBakery\Support
 */
class Collection extends \Illuminate\Support\Collection
{
    /**
     * @param string $string
     * @param string $separator
     *
     * @return Collection
     */
    public static function explode(string $string, string $separator): Collection
    {
        return new self(explode($separator, $string));
    }

    /**
     * @param bool $ucFirst
     *
     * @return string
     */
    public function camel($ucFirst = true): string
    {
        $string = Str::camel($this->implode(' '));

        if ($ucFirst) {
            $string = Str::ucfirst($string);
        }

        return $string;
    }

    /**
     * @return string
     */
    public function snake(): string
    {
        return Str::snake($this->implode(' '));
    }

    /**
     * @return string
     */
    public function kebab(): string
    {
        return Str::kebab($this->implode(' '));
    }
}
