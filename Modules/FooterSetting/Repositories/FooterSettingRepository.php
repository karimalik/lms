<?php

namespace Modules\FooterSetting\Repositories;


class FooterSettingRepository
{


    public function update($data, $id)
    {
        UpdateGeneralSetting($data['key'], $data['value']);
    }

    public function edit($id)
    {
        $footer = $this->footer->findOrFail($id);
        return $footer;
    }
}
