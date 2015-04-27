
public function setCroppable<?php echo $image_column; ?>($data)
{
    if (!is_array($data)) {
        $data = array();
    }

    if (!isset($data['image'])) {
        $data['image'] = null;
    }

    if (!isset($data['coordinates'])) {
        $data['coordinates'] = null;
    }

    $this->set<?php echo $image_column; ?>($data['image']);
    $this->set<?php echo $coordinates_column; ?>($data['coordinates']);
}
