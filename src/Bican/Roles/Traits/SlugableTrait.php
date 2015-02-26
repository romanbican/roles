<?php namespace Bican\Roles\Traits;

trait SlugableTrait {

    /**
     * Set slug property.
     *
     * @param string $value
     * @return void
     */
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = str_slug($value, '.');
    }
}