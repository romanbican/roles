<?php

namespace Bican\Roles\Traits;

trait SlugableTrait
{
    /**
     * Set slug attribute.
     *
     * @param string $value
     * @return void
     */
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = str_slug($value, config('roles.separator'));
    }
}
