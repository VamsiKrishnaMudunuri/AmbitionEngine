<div class="row">
    <div class="col-md-12">
        <div class="copyright text-center"> {{ Translator::transSmart('app.common_copyright', '', false, ['year' => \Carbon\Carbon::today()->format('Y'), 'name' => Utility::constant('app.title.name')]) }}</div>
    </div>
</div>