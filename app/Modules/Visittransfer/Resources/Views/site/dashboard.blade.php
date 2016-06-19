@extends('layout')

@section('content')
    <div class="row">
        <div class="col-md-4 hidden-xs">
            {!! HTML::panelOpen("Visiting", ["type" => "vuk", "key" => "letter-v"]) !!}
                    <!-- Content Of Panel [START] -->
            <!-- Top Row [START] -->
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <p>
                        You can apply to become a visitor if you:
                    <ul>
                        <li>want to control <strong>specific facilities*</strong> within the UK</li>
                        <li><strong>do not</strong> wish to leave your current division</li>
                    </ul>
                    <small>*Each facility will require a separate application.</small>
                    </p>
                </div>

            </div>

            <br/>

            <div class="row">
                <div class="col-xs-12 text-center">
                    @can("create", new \App\Modules\Visittransfer\Models\Application)
                        {!! Button::success("START APPLICATION")->asLinkTo(route("visiting.application.start", [\App\Modules\Visittransfer\Models\Application::TYPE_VISIT])) !!}
                    @elseif($currentVisitApplication)
                        @if($currentVisitApplication->is_in_progress)
                            {!! Button::primary("CONTINUE APPLICATION")->asLinkTo(route("visiting.application.continue")) !!}
                        @else
                            {!! Button::danger("You currently have a visit application open.")->disable() !!}
                        @endif
                    @elseif($currentTransferApplication)
                        {!! Button::danger("You currently have a transfer application open.")->disable() !!}
                    @else
                        {!! Button::danger("You are not able to apply to visit at this time.")->disable() !!}
                    @endcan
                </div>
            </div>

            {!! HTML::panelClose() !!}
        </div>

        <div class="col-md-4 hidden-xs">
            {!! HTML::panelOpen("Transferring", ["type" => "vuk", "key" => "letter-t"]) !!}
                    <!-- Content Of Panel [START] -->
            <!-- Top Row [START] -->
            <div class="row">
                <div class="col-md-10 col-xs-offset-1">
                    <p>
                        You can apply to transfer if you:
                    <ul>
                        <li>want the freedom to <strong>control anywhere</strong> within the UK*</li>
                        <li><strong>are happy</strong> to leave your current division</li>
                    </ul>
                    <small>*subject to appropriate training and GRP restrictions.</small>
                    </p>
                </div>

            </div>

            <br/>

            <div class="row">
                <div class="col-xs-12 text-center">
                    @can("create", new \App\Modules\Visittransfer\Models\Application)
                        {!! Button::success("START APPLICATION")->asLinkTo(route("visiting.application.start", [\App\Modules\Visittransfer\Models\Application::TYPE_TRANSFER])) !!}
                    @elseif($currentTransferApplication)
                        {!! Button::primary("CONTINUE APPLICATION")->asLinkTo(route("visiting.application.continue")) !!}
                    @elseif($currentVisitApplication)
                        {!! Button::danger("You currently have a visit application open.")->disable() !!}
                    @else
                        {!! Button::danger("You are not able to apply to transfer at this time.")->disable() !!}
                        @endcan
                </div>
            </div>

            {!! HTML::panelClose() !!}
        </div>

        <div class="col-md-4 hidden-xs">
            {!! HTML::panelOpen("What about pilots?", ["type" => "vuk", "key" => "letter-p"]) !!}

            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <p>
                        In order to start training towards your pilot ratings in VATSIM UK you <strong>must</strong>
                        be a visiting member.  You must submit a <strong>visiting application</strong> using this system.
                    </p>
                    <br />
                    <p>
                        If you are also planning on applying to control within the United Kingdom it is
                        <strong>highly recommended</strong> that you complete your visiting
                        application as a pilot first.
                    </p>
                </div>
            </div>

            {!! HTML::panelClose() !!}
        </div>

        <div class="col-xs-12 visible-xs">
            {!! HTML::panelOpen("Start a new Application", ["type" => "fa", "key" => "exclamation"]) !!}
                <p class="text-center">You can only complete your application and references on a non-mobile device.</p>
            {!! HTML::panelClose() !!}
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            {!! HTML::panelOpen("Application History", ["type" => "fa", "key" => "list-alt"]) !!}
            <div class="row">
                <div class="col-md-10 col-md-offset-1">

                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                        <tr>
                            <th width="5%">ID</th>
                            <th width="10%">Type</th>
                            <th width="25%">Facility</th>
                            <th width="10%" class="hidden-xs hidden-sm">Submitted</th>
                            <th>Outcome</th>
                            <th class="text-center">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        @if(count($allApplications) < 1)
                            <tr><td colspan="6" class="text-center">You have no applications to display.</td></tr>
                        @else
                            @foreach($allApplications as $application)
                                <tr>
                                    <td>{{ $application->id }}</td>
                                    <td>{{ $application->type_string }}</td>
                                    <td>{{ $application->facility ? $application->facility->name : "None Selected" }}</td>
                                    <td class="hidden-xs hidden-sm">
                                        @if($application->submitted_at == null)
                                            Not yet submitted
                                        @else
                                            <span class="hidden-xs">{{ $application->submitted_at }}</span>
                                            <span class="visible-xs">{{ $application->submitted_at->toFormattedDateString() }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($application->is_open)
                                            <span class="btn btn-success btn-xs text-center">{{ $application->status_string }}</span>
                                            <span class="hidden-xs hidden-sm">
                                                Some text.
                                            </span>
                                        @elseif($application->requires_action)
                                            <span class="btn btn-warning btn-xs text-center">{{ $application->status_string }}</span>
                                            <span class="hidden-xs hidden-sm">
                                                Some text.
                                            </span>
                                        @else
                                            <span class="btn btn-danger btn-xs text-center">{{ $application->status_string }}</span>
                                            <span class="hidden-xs hidden-sm">
                                                Some text.
                                            </span>
                                        @endif

                                    </td>
                                    <td class="text-center">
                                        @if($application->is_in_progress)
                                            {!! link_to_route("visiting.application.continue", "Continue") !!}
                                        @else
                                            {!! link_to_route("visiting.application.view", "View", [$application->id]) !!}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>

            </div>
            {!! HTML::panelClose() !!}
        </div>
    </div>
@stop
