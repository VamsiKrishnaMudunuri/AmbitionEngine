<div class="job-header">
    <div class="job-location">{{ $job->place }}</div>
    <div class="job-title">{{ $job->title }}</div>
</div>

<div class="job-description m-t-5-full">
    <h4 style="font-weight: bold">

        {{ Translator::transSmart('app.Job Description', 'Job Description')}}

    </h4>

    @if ($job->overview)
        {{ $job->overview }}
        <br/>
        <br/>
    @endif

    {!! $job->content !!}
</div>

<br/>

<div class="divider-row" style="border-bottom: 1px solid rgba(156, 120, 52, .2)"></div>

<br/>

{{ Html::linkRoute('page::career::job::contact', Translator::transSmart("app.Apply Now", "Apply Now"), ['job' => $job->getKey()], ['title' => Translator::transSmart("app.Back to Blog", "Back to Blog"), 'class' => 'btn btn-green m-t-20']) }}