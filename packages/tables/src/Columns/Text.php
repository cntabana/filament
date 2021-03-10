<?php

namespace Filament\Tables\Columns;

use Carbon\Carbon;
use Illuminate\Support\Str;

class Text extends Column
{
    use Concerns\CanCallAction;
    use Concerns\CanOpenUrl;

    public $default;

    public $formatUsing;

    public $trimLength = -1;

    public $trimmable = false;

    public function currency($symbol = '$', $decimalSeparator = '.', $thousandsSeparator = ',')
    {
        $this->formatUsing = function ($value) use ($decimalSeparator, $symbol, $thousandsSeparator) {
            if (! is_numeric($value)) return $this->default;

            return $symbol.number_format($value, 2, $decimalSeparator, $thousandsSeparator);
        };

        return $this;
    }

    public function date($format = 'F j, Y')
    {
        $this->formatUsing = function ($value) use ($format) {
            $value = Carbon::parse($value)->format($format);

            return $value;
        };

        return $this;
    }

    public function dateTime($format = 'F j, Y H:i:s')
    {
        $this->date($format);

        return $this;
    }

    public function default($default)
    {
        $this->default = $default;

        return $this;
    }

    public function formatUsing($callback)
    {
        $this->formatUsing = $callback;

        return $this;
    }

    public function getValue($record, $attribute = null)
    {
        $value = parent::getValue($record, $attribute);

        if ($value === null) {
            return value($this->default);
        }

        if ($this->formatUsing) {
            $callback = $this->formatUsing;

            $value = $callback($value);
        }

        if (! $this->trimmable) return $value;
        if (! is_int($this->trimLength)) return $value;

        return Str::of($value)->limit($this->trimLength);
    }

    public function options($options)
    {
        $this->formatUsing = function ($value) use ($options) {
            if (array_key_exists($value, $options)) {
                return $options[$value];
            }

            return $value;
        };

        return $this;
    }

    public function trim($trimLength = -1)
    {
        $this->trimLength = $trimLength;
        $this->trimmable = true;

        return $this;
    }
}
