<?php

namespace Modules\Setting\Repositories;


class GeneralSettingRepository implements GeneralSettingRepositoryInterface
{


    public function update(array $data)
    {
        foreach ($data as $key => $value) {
            UpdateGeneralSetting($key, $value);
        }
        return true;
    }
}
