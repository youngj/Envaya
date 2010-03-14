<?php
    $current = $vars['current'];
    
    $deletename = $vars['deletename'];
    $deleteid = $vars['deleteid'];
    
    $imageInput = elgg_view("input/file", array(
        'internalname' => $vars['internalname'],
        'internalid' => $vars['internalid'],
        'js' => $vars['js']
    )); 
?>

<?php if ($current) { ?>
    <table>
    <tr>
        <td style='padding-right:10px;width:100px'><?php echo elgg_echo('image:current') ?><br />
            <img src='<?php echo $current ?>' />
        </td>
        <td> 
            <div>
                <?php echo elgg_echo('image:new') ?>
                    <?php echo $imageInput ?>
                        <div class='help'><?php echo elgg_echo('image:blank') ?></div>
            </div>                                    
            <div style='padding-top:10px'>
            <?php echo elgg_view('input/checkboxes', 
            array('internalname' => $deletename,
                'internalid' => $deleteid,
                'options' => array(elgg_echo('image:delete')),
                'js' => $vars['js']
            )) ?>
            </div>

        </td>
    </tr>
    </table>    

<?php } else { ?>        
    <?php echo $imageInput ?>    
    <div class='help'><?php echo elgg_echo('image:optional') ?></div>
<?php } ?>
