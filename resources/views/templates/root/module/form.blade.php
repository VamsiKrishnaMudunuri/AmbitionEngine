{{ Html::success() }}
{{ Html::error() }}
{{Html::validation($module, 'csrf_error')}}
{{ Form::open(array('route' => $route))}}

    <div class="row">
        <div class="col-sm-12">

            <div class="form-group required">
                {{Html::validation($module, 'name')}}
                <label for="name" class="control-label">{{Translator::transSmart('app.Name', 'Name')}}</label>
                {{Form::text('name', $module->name , array('id' => 'name', 'class' => 'form-control', 'maxlength' => $module->getMaxRuleValue('name'), 'title' => Translator::transSmart('app.Name', 'Name')))}}
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                {{Html::validation($module, 'description')}}
                <label for="description" class="control-label">{{Translator::transSmart('app.Description', 'Description')}}</label>
                {{Form::textarea('description', $module->description , array('id' => 'description', 'class' => 'form-control', 'maxlength' => $module->getMaxRuleValue('description'), 'title' => Translator::transSmart('app.Description', 'Description')))}}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group required">
                {{Html::validation($module, 'controller')}}
                <label for="controller" class="control-label">{{Translator::transSmart('app.Controller', 'Controller')}}</label>
                {{Form::text('controller', $module->controller , array('id' => 'controller', 'class' => 'form-control', 'maxlength' => $module->getMaxRuleValue('controller'), 'title' => Translator::transSmart('app.Controller', 'Controller')))}}
                <span class="help-block">{{Translator::transSmart("app.Make sure controller class is created. Otherwrise it won't work for access right mechanism.", "Make sure controller class is created. Otherwrise it won't work for access right.")}}</span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group required">
                {{Html::validation($module, 'rights')}}
                <label for="rights" class="control-label">{{Translator::transSmart('app.Rights', 'Rights')}}</label>
                {{Form::text('rights', $module->rights , array('id' => 'rights', 'class' => 'form-control', 'title' => Translator::transSmart('app.Rights', 'Rights')))}}
                <span class="help-block">{{Translator::transSmart('app.Separate right by comma. Example: read,write,delete.', 'Separate right by comma. Example: read,write,delete.')}}</span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                {{Html::validation($module, 'icon')}}
                <label for="icon" class="control-label">{{Translator::transSmart('app.Icon', 'Icon')}}</label>
                {{Form::text('icon', $module->icon , array('id' => 'icon', 'class' => 'form-control', 'maxlength' => $module->getMaxRuleValue('icon'), 'title' => Translator::transSmart('app.Icon', 'Icon')))}}
                <span class="help-block">{{Translator::transSmart('app.Only fontawesome is supported.', 'Only fontawesome is supported.')}}</span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group required">
                {{Html::validation($module, 'is_module')}}
                <label for="is_module" class="control-label">{{Translator::transSmart('app.Module', 'Module')}}</label>
                <br />
                <div class="radio-inline">

                        {{Form::radio('is_module', Utility::constant('module.admin.slug') , ($module->is_module == Utility::constant('module.admin.slug')) ? true : false, array('title' => Translator::transSmart('app.Module', 'Module')))}} {{Utility::constant('module.admin.name')}}

                </div>
                <div class="radio-inline">

                        {{Form::radio('is_module', Utility::constant('module.member.slug') , ($module->is_module == Utility::constant('module.member.slug')) ? true : false, array('title' => Translator::transSmart('app.Module', 'Module')))}} {{Utility::constant('module.member.name')}}

                </div>
                <div class="radio-inline">

                    {{Form::radio('is_module', Utility::constant('module.agent.slug') , ($module->is_module == Utility::constant('module.agent.slug')) ? true : false, array('title' => Translator::transSmart('app.Module', 'Module')))}} {{Utility::constant('module.agent.name')}}

                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group text-center">
                <div class="btn-group">
                    {{Form::submit($submit_text, array('title' => $submit_text, 'class' => 'btn btn-theme btn-block'))}}
                </div>
                <div class="btn-group">
                    {{Form::submit(Translator::transSmart('app.Cancel', 'Cancel'), array('title' => Translator::transSmart('app.Cancel', 'Cancel'), 'class' => 'btn btn-theme btn-block', 'onclick' => 'location.href="' .  URL::getLandingIntendedUrl($url_intended, URL::route('root::module::index')) . '"; return false;')) }}
                </div>
            </div>
        </div>
    </div>
{{ Form::close() }}