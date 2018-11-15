<section class="section">
    <div class="container">
        <div class="row">
            <div class="col-md-12 d-flex justify-content-center align-content-center form-auth-container flex-column">
                <div class="row">
                    <div class="col-md-12">
                        <div class="page-header border-b-no text-center">
                            <div class="text-green">
                                <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-thumbs-up"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path></svg>
                            </div>
                            <h3>
                                <b>
                                    {{ Translator::transSmart("app.Thank You", "Thank You") }}
                                </b>
                            </h3>
                        </div>
                        <div class="text-center">
                            {{ Translator::transSmart("app.We will contact your friend shortly.", "We will contact your friend shortly") }}<br/>
                            {{ Translator::transSmart("app.Will redirect to previous page in %s seconds", sprintf("Will redirect to previous page in %s seconds", '<span id="timer">12</span>'), true) }}.
                            <br/>
                            <br/>
                            <div class="m-t-10-full">
                                <input type="hidden" id="back-url" value="{{URL::previous()}}" />
                                <a class="btn btn-green" href="{{URL::previous()}}" role="button">{{Translator::transSmart('app.Back to Previous Page', 'Back to Previous Page')}}</a>
                                <a class="btn btn-green" href="{{ route('page::index') }}" role="button">{{Translator::transSmart('app.Back to Home', 'Back to Home')}}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>