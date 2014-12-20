<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>
<div class="row">
      <div class="col-xs-8">
        <div id="d3container"></div>
        <div class="row">
          <div class="col-xs-3"> 
          <small>For delete repation double-clicking on line.<br>
          For connect move node to other node.</small>
          </div>
          
           <div class="col-xs-9">
             <small id="status"></small>
           </div>
        </div>
      </div>
      <div class="col-xs-4 panel">
        <h4 class="page-header">Positions</h4>
        <button class="btn btn-success" onclick="localStorage.setItem('nodes',JSON.stringify(force.nodes()));localStorage.setItem('links',JSON.stringify(force.links()));">Save to localStorage</button>
        <button class="btn btn-dunger" onclick="localStorage.setItem('nodes',null);localStorage.setItem('links',null);">Reset localStorage</button>
        <h4 class="page-header">Info</h4>
        <pre id="infoItem"></pre>
          
        <h4 class="page-header">Dashboard</h4>
        
           <?php 
           $form = ActiveForm::begin(['id'=>"mainForm"]); 
           $model = new app\modules\rbac\models\ItemForm(null)
           ?>
            
            <?= $form->field($model, 'type')->dropDownList([1=>"Role",2=>"Permission"],['size'=>2]) ?>
            
            <?= Html::activeHiddenInput($model, 'oldName') ?>
            
            <?= $form->field($model, 'name')->textInput(['maxlength' => 64]) ?>
        
            <?= $form->field($model, 'description')->textarea(['rows' => 2]) ?>
        
            <?= $form->field($model, 'ruleName') ?>
        
            <?= $form->field($model, 'data')->textarea(['rows' => 2]) ?>
        
            <div class="form-group">
                <?=
                Html::button('Save', [
                    'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                    'id'=>'submitForm',])
                ?>
            </div>
        
            <?php ActiveForm::end(); ?>
        
        <h4 class="page-header">Basic Concepts</h4>
        
        <p>
            A role represents a collection of permissions (e.g. creating posts, updating posts). A role may be assigned
            to
            one or multiple users. To check if a user has a specified permission, we may check if the user is assigned
            with
            a role that contains that permission.
        </p>

        <p>
            Associated with each role or permission, there may be a rule. A rule represents a piece of code that will be
            executed during access check to determine if the corresponding role or permission applies to the current
            user.
            For example, the "update post" permission may have a rule that checks if the current user is the post
            creator.
            During access checking, if the user is NOT the post creator, he/she will be considered not having the
            "update
            post" permission.
        </p>

        <p>
            Both roles and permissions can be organized in a hierarchy. In particular, a role may consist of other roles
            or
            permissions; and a permission may consist of other permissions. Yii implements a partial order hierarchy
            which
            includes the more special tree hierarchy. While a role can contain a permission, it is not true vice versa.
        </p>
        
      </div>
    </div>