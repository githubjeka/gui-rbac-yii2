<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

?>
<div class="row">
    <div class="col-md-8">
        <div id="d3container"></div>
        <div class="row">
            <div class="col-xs-4">
                <small>
                    Double-click on node for delete or update<br>
                    To delete relation double-click on the connection.<br>
                    To connect move node to other node.
                </small>
            </div>

            <div class="col-xs-8">
                <svg width="100%" height="70px">
                    <g class="legend" transform="translate(0,22)">
                        <rect width="18" height="18" class="roleIcon"></rect>
                        <text x="22" y="14">Role</text>
                    </g>
                    <g class="legend" transform="translate(0,0)">
                        <rect width="18" height="18" class="permissionIcon"></rect>
                        <text x="22" y="14">Permission</text>
                    </g>
                    <g class="legend" transform="translate(0,44)">
                        <path class="linkLegend" d="M 0 7 L 20 7" marker-start="url(#marker)"></path>
                        <text x="22" y="14">Connection item to other item</text>
                    </g>
                    <g class="legend" transform="translate(250,22)">
                        <path class="linkLegend roleLinkLegend" d="M 0 7 L 40 7" marker-start="url(#marker)"></path>
                        <text x="45" y="14">Connection to a role</text>
                    </g>
                    <g class="legend" transform="translate(250,0)">
                        <path class="linkLegend permissionLinkLegend" d="M 0 7 L 40 7" marker-start="url(#marker)"></path>
                        <text x="45" y="14">Connection to a permission</text>
                    </g>
                     <g class="legend" transform="translate(250,44)">
                        <path class="linkLegend permissionLinkLegend childLinkLegend" d="M 0 7 L 40 7" marker-start="url(#marker)"></path>
                        <text x="45" y="14">Dotted line mean connection child to his parent</text>
                    </g>
                </svg>
            </div>
        </div>
    </div>
    <div class="col-md-4 panel">
        <!--<h4 class="page-header">Positions</h4>-->
        <!--<button class="btn btn-success" onclick="localStorage.setItem('nodes',JSON.stringify(force.nodes()));localStorage.setItem('links',JSON.stringify(force.links()));">Save to localStorage</button>-->
        <!--<button class="btn btn-dunger" onclick="localStorage.setItem('nodes',null);localStorage.setItem('links',null);">Reset localStorage</button>-->
        <h4 class="page-header">Info <small class="sub-header">click on element</small></h4>
        <pre id="infoItem"></pre>

        <h4 class="page-header">Dashboard</h4>

        <?php
        $form = ActiveForm::begin(['id' => "mainForm"]);
        $model = new app\modules\rbac\models\ItemForm(null)
        ?>

        <?= $form->field($model, 'type')->dropDownList([1 => "Role", 2 => "Permission"], ['size' => 2]) ?>

        <?= $form->field($model, 'oldName')->textInput(['readonly' => 'readonly']) ?>

        <?= $form->field($model, 'name')->textInput(['maxlength' => 64]) ?>

        <?= $form->field($model, 'description')->textarea(['rows' => 2]) ?>

        <?= $form->field($model, 'ruleName') ?>

        <?= $form->field($model, 'data')->textarea(['rows' => 2]) ?>

        <div class="form-group">
            <?=
            Html::button(
                'Save',
                [
                    'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                    'id' => 'submitForm',
                ]
            )
            ?>
            <button type="reset" class="btn btn-default">Reset form</button>
            <button type="button" class="btn btn-danger" id="deleteForm">Delete item</button>
        </div>

        <?php ActiveForm::end(); ?>

        <h4 class="page-header">
            <a href="https://github.com/yiisoft/yii2/blob/master/docs/guide/security-authorization.md#role-based-access-control-rbac">
                Basic Concepts
            </a>
        </h4>
        
    </div>
</div>