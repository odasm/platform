<?php
$this->extend('/Common/blank');
?>
<style type='text/css'>
    #GameAdminEditForm label {
        font-weight: bold;
    }
    .config-box {
        display: none;
    }
    .label {
        font-weight: bold;
    }
</style>
<h3 class='page-header'>SDK Edit Game</h3>
<div class="row">
    <div class="container">
        <div class="span6">
            <table class="table table-hover">
                <tr>
                    <td><b>Game</b></td>
                    <td><?php echo $this->request['data']['Game']['title_os']; ?></td>
                </tr>
                <tr>
                    <td><b>App Key</b></td>
                    <td><?php echo $this->request['data']['Game']['app'] ?></td>
                </tr>
                <tr>
                    <td><b>Secret Key</b></td>
                    <td><?php echo $this->request['data']['Game']['secret_key'] ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
<div class="games form">

<?php
echo $this->Form->create('Game', array(
    'type' => 'file'));
?>
<div class='row'>
    <div class='span3'>
        <?php echo $this->Form->input('id'); ?> <br/>

        <div class='row'>
            <div class='span3'>
                <a href="#" class='show-config-box'>Payment Test account</a>
                <div class='config-box'>
                    <?php
                            echo $this->Form->input('Game.data.payment.testallowed', array('label' => 'Test account', 'type' => 'textarea'));
                    ?>
                </div>
            </div>
        </div>
    </div>

    <div class="span3">
        <?php
            echo $this->Form->input('fbpage_id',array('type'=>'text','readonly' => 'readonly','label'=> '<strong>Fbpage ID</strong>'));
            echo $this->Form->input('app_gaid', array('label' => '<strong>App Ga ID</strong>'));
        ?><br/>
    </div>
</div>

<?php echo $this->Form->submit('Submit', array('class' => 'btn btn-primary')); ?>
<?php echo $this->Form->end() ?>
</div>


<script type="text/javascript">
    $(function() {
        $(".show-config-box").click(function() {
            var boxconfig = $(this).next();
            if (boxconfig.is(':visible')) {
                boxconfig.hide();
            } else {
                boxconfig.show();
            }
            return false;
        });
    })
</script>