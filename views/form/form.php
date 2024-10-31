<?php 
if ( ! defined( 'ABSPATH' ) ) exit;


foreach($formData as $data) {
    ?>
    <div class="form-group mb-3">
        <label class="form-label"><?php echo esc_html($data['name']);?> <?php if($data['is_required'] == 'optional'){ ?><span class="form-optional"><?php esc_html_e('(Optional)','ovoform') ?></span> <?php } ?></label>
        <?php if($data['type'] == 'text') { ?>
            <input type="text"
            class="form-control form--control"
            name="<?php echo esc_attr($data['label']);?>" <?php if($data['is_required'] == 'required'){ echo 'required'; }?>>
        <?php }elseif($data['type'] == 'textarea'){?>
            <textarea
                class="form-control form--control"
                name="<?php echo esc_attr($data['label']);?>"
                <?php if($data['is_required'] == 'required') echo 'required' ?>
            ></textarea>
        <?php } elseif($data['type'] == 'select'){?>
            <select
                class="form-control form--control form-select"
                name="<?php echo esc_attr($data['label']);?>"
                <?php if($data['is_required'] == 'required') echo 'required';?>
            >
                <option value=""><?php esc_html_e('Select One', 'ovoform');?></option>
                <?php foreach ($data['options'] as $item){ ?>
                    <option value="<?php echo esc_attr($item);?>"><?php echo esc_html($item);?></option>
                <?php } ?>
            </select>
        <?php } elseif($data['type'] == 'checkbox') {?>
            <?php foreach($data['options'] as $option){ ?>
                <div class="form-check">
                    <input
                        class="form-check-input"
                        name="<?php echo esc_attr($data['label']);?>[]"
                        type="checkbox"
                        value="<?php echo esc_attr($option);?>"
                        id="<?php echo esc_attr($data['label'] .'_'.ovoform_title_to_key($option));?>"
                    >
                    <label class="form-check-label" for="<?php echo esc_attr($data['label'].'_'.ovoform_title_to_key($option));?>"><?php echo esc_html($option);?></label>
                </div>
            <?php } ?>
        <?php } elseif($data['type'] == 'radio'){ ?>
            <?php foreach($data['options'] as $option){?>
                <div class="form-check">
                    <input
                    class="form-check-input"
                    name="<?php echo esc_attr($data['label']);?>"
                    type="radio"
                    value="<?php echo esc_attr($option);?>"
                    id="<?php echo esc_attr($data['label'].'_'.ovoform_title_to_key($option));?>">
                    <label class="form-check-label" for="<?php echo esc_attr($data['label'].'_'.ovoform_title_to_key($option));?>">
                        <?php echo esc_html($option);?>
                    </label>
                </div>
            <?php } ?>
        <?php } elseif($data['type'] == 'file') { ?>
            <input
            type="file"
            class="form-control form--control"
            name="<?php echo esc_attr($data['label']);?>"
            <?php if($data['is_required'] == 'required') echo 'required';?>
            accept="<?php foreach(explode(',',$data['extensions']) as $ext){ echo '.' . printf(esc_html__('%s', 'ovoform'),  esc_html($ext)) . ', ';?> <?php } ?>"
            >
            <pre class="text-primary mt-1"><?php esc_html_e('Supported mimes', 'ovoform');?>: <?php echo esc_html($data['extensions']);?></pre>
        <?php } ?>
    </div>
<?php } ?>