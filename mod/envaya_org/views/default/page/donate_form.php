
<form style='padding-top:10px' id='donate_form' method="POST" action="/org/submit_donate_form">
    
    <input name="projectID" type="hidden" value="envaya" />

    <div class='input'>
        <label>Donation Amount</label>
        
        <table id='donation_amount'>
        <tr>
        <?php
            $last_amount = restore_input('_amount', null);
                        
            function amount_button($amount, $last_amount)
            {
                $selected = ($amount == $last_amount) ? "checked='checked'" : '';
                echo "<input type='radio' class='input-radio' name='_amount' value='$amount' $selected />";
            }
        ?>
        
        <td style='padding-right:20px'>
            <label><?php echo amount_button('25', $last_amount); ?> $25</label><br />
            <label><?php echo amount_button('250', $last_amount); ?> $250</label>
        </td>
        <td style='padding-right:20px'>
            <label><?php echo amount_button('50', $last_amount); ?> $50</label>  <br />
            <label><?php echo amount_button('500', $last_amount); ?> $500</label>
        </td>
        <td style='padding-right:20px'>
            <label><?php echo amount_button('100', $last_amount); ?> $100</label><br />
            <label><?php echo amount_button('1000', $last_amount); ?> $1000</label>
        </td>
        <td>
        <label><?php echo amount_button('other', $last_amount); ?> Other Amount</label><br />
        $ <?php echo view('input/text', array(
            'name' => "_other_amount", 
            'style' => "width:100px;margin-top:0px;"
        )); ?>         
        </td>
        </tr>
        </table>                
        <!-- <input type="hidden" id='donation' name="donation" size="25" maxlength="25"> -->
    </div>    

    <div class='input'>
    <label>Personal Information</label>
    <table>
    <tr>
    <td>    
        <div class='input'>
            Full name<br />
            <?php echo view('input/text', array(
                'name' => "full_name",
                'maxlength' => 75,
            )); ?> 
        </div>

        <div class='input'>
            Email address<br />
            <?php echo view('input/text', array(
                'name' => "email",
                'maxlength' => 75,
            )); ?> 
        </div>  
        
        <div class='input'>
            Phone number<br />
            <?php echo view('input/text', array(
                'name' => "phone",
                'maxlength' => 75,
            )); ?>
        </div>
        
        <div class='input'>
            Organization <span class='help'>(optional)</span><br />
            <?php echo view('input/text', array(
                'name' => "company",
                'maxlength' => 75,
            )); ?>            
        </div>        

        <div class='input'>
            Website <span class='help'>(optional)</span><br />
            <?php echo view('input/text', array(
                'name' => "website",
            )); ?>                        
        </div>    

    </td>
    <td id='donor_address'>
        <div class='input'>
            Street address<br />
            <?php echo view('input/text', array(
                'name' => "address",
                'maxlength' => 75,
            )); ?>             
        </div>        
        
        <div class='input'>
            Street address <span class='help'>(optional)</span><br />
            <?php echo view('input/text', array(
                'name' => "address2",
                'maxlength' => 75,
            )); ?>
		</div>        
         
        <div class='input'>
            City<br />
            <?php echo view('input/text', array(
                'name' => "city",
                'maxlength' => 75,
            )); ?>
        </div>                
        
        <table>
        <tr>
        <td style='width:130px'>
            <div class='input'>
                State/Province<br />
                <?php echo view('input/text', array(
                    'name' => "state",
                    'maxlength' => 75,
                    'style' => 'width:95px',
                )); ?>                
            </div>
        </td>
        <td>
            <div class='input'>
                Zip/Postal Code<br />
                <?php echo view('input/text', array(
                    'name' => "zip",
                    'maxlength' => 75,
                    'style' => 'width:100px',
                )); ?>                                
            </div>
        </td>
        </tr>
        </table>

        <div class='input'>
            Country<br />
                <?php echo view('input/text', array(
                    'name' => "country",
                    'maxlength' => 75,
                )); ?>                                            
        </div>       

    </td>
    </tr>
    </table>
    </div>
        
    <input type="hidden" name="Fax" value="">
    <input type="hidden" name="Payment" value="Visa/Mastercard">
    
    <table>
    <tr>
    <td>
    <?php
        echo view('input/submit', array(
            'name' => null,
            'value' => "Continue to Payment Info",
        ));    
    ?>
    </td>
    <td style='padding-left:20px'>
    
    <div class='help' style='font-size:12px;padding-top:6px;'><strong>Note:</strong> 
    Donations to Envaya are processed by 
    the <a target='_blank' href='http://www.trustforconservationinnovation.org/'>Trust for Conservation Innovation</a>. You will be redirected to 
    their website to complete your donation. 
    </div>
    </tr>
    </table>   
    
    <div style='width:1px;height:1px;overflow:hidden'><iframe src='/org/tci_donate_frame' frameborder='0' border='0'></iframe></div>
</form>