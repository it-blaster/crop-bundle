
public function getCroppable<?php echo $image_column; ?>()
{
    return array(
        'image'       => $this->get<?php echo $image_column; ?>(),
        'coordinates' => $this->get<?php echo $coordinates_column; ?>()
    );
}
