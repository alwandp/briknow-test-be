@forelse($data as $proj)
    <!-- MODAL LOG STATUS -->
        <div class="modal fade modal_log" id="modal-log-status{{$proj->id}}" tabindex="-1" aria-labelledby="modal-log-statusLabel{{$proj->id}}" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="card-body">
                    <h5 class="text-center">Log Status</h5>
                    <div class="stepwizard align-items-center">
                        @if($proj->flag_mcs == 1)
                            <div class="stepwizard-row setup-panel">
                            <div class="stepwizard-step s2 stepwizard-step-garis">
                                <a href="#step-1" id="s-1" class="btn bunderan btn-default btn-circle disable">&nbsp;</a>
                                <p class="ket my-0 text-secondary">Checker</p>
                            </div>
                            <div class="stepwizard-step s3 stepwizard-step-garis">
                                <a href="#step-2" id="s-2" class="btn bunderan btn-default btn-circle disable" style="pointer-events: none">&nbsp;</a>
                                <p class="ket my-0 text-secondary">Signer</p>
                            </div>
                            <div class="stepwizard-step s4 stepwizard-step-garis">
                                <a href="#step-3" id="s-3" class="btn bunderan btn-default btn-circle disable" style="pointer-events: none">&nbsp;</a>
                                <p class="ket my-0 text-secondary">Admin</p>
                            </div>
                            <div class="stepwizard-step s5">
                                <a href="#step-3" id="s-4" class="btn bunderan btn-default btn-circle disable" style="pointer-events: none">&nbsp;</a>
                                <p class="ket my-0 text-secondary">Publish</p>
                            </div>
                            </div>
                        @elseif($proj->flag_mcs == 2)
                            <div class="stepwizard-row setup-panel">
                                <div class="stepwizard-step s2 stepwizard-step-success">
                                    <a href="#step-1" id="s-1" class="btn bunderan btn-success btn-circle reguler">&nbsp;</a>
                                    <p class="ket my-0 text-secondary">Checker</p>
                                </div>
                                <div class="stepwizard-step s3 stepwizard-step-garis">
                                    <a href="#step-2" id="s-2" class="btn bunderan btn-default btn-circle disable" style="pointer-events: none">&nbsp;</a>
                                    <p class="ket my-0 text-secondary">Signer</p>
                                </div>
                                <div class="stepwizard-step s4 stepwizard-step-garis">
                                    <a href="#step-3" id="s-3" class="btn bunderan btn-default btn-circle disable" style="pointer-events: none">&nbsp;</a>
                                    <p class="ket my-0 text-secondary">Admin</p>
                                </div>
                                <div class="stepwizard-step s5">
                                    <a href="#step-3" id="s-4" class="btn bunderan btn-default btn-circle disable" style="pointer-events: none">&nbsp;</a>
                                    <p class="ket my-0 text-secondary">Publish</p>
                                </div>
                            </div>
                        @elseif($proj->flag_mcs == 3)
                            <div class="stepwizard-row setup-panel">
                                <div class="stepwizard-step s2 stepwizard-step-success">
                                    <a href="#step-1" id="s-1" class="btn bunderan btn-success btn-circle reguler">&nbsp;</a>
                                    <p class="ket my-0 text-secondary">Checker</p>
                                </div>
                                <div class="stepwizard-step s3 stepwizard-step-success">
                                    <a href="#step-2" id="s-2" class="btn bunderan btn-success btn-circle reguler" style="pointer-events: none">&nbsp;</a>
                                    <p class="ket my-0 text-secondary">Signer</p>
                                </div>
                                <div class="stepwizard-step s4 stepwizard-step-garis">
                                    <a href="#step-3" id="s-3" class="btn bunderan btn-default btn-circle reguler" style="pointer-events: none">&nbsp;</a>
                                    <p class="ket my-0 text-secondary">Admin</p>
                                </div>
                                <div class="stepwizard-step s5">
                                    <a href="#step-3" id="s-4" class="btn bunderan btn-default btn-circle disable" style="pointer-events: none">&nbsp;</a>
                                    <p class="ket my-0 text-secondary">Publish</p>
                                </div>
                            </div>
                        @elseif($proj->flag_mcs == 4)
                            <div class="stepwizard-row setup-panel">
                                <div class="stepwizard-step s2 stepwizard-step-success">
                                    <a href="#step-1" id="s-1" class="btn bunderan btn-success btn-circle reguler">&nbsp;</a>
                                    <p class="ket my-0 text-secondary">Checker</p>
                                </div>
                                <div class="stepwizard-step s3 stepwizard-step-success">
                                    <a href="#step-2" id="s-2" class="btn bunderan btn-success btn-circle reguler" style="pointer-events: none">&nbsp;</a>
                                    <p class="ket my-0 text-secondary">Signer</p>
                                </div>
                                <div class="stepwizard-step s4 stepwizard-step-success">
                                    <a href="#step-3" id="s-3" class="btn bunderan btn-success btn-circle reguler" style="pointer-events: none">&nbsp;</a>
                                    <p class="ket my-0 text-secondary">Admin</p>
                                </div>
                                <div class="stepwizard-step s5">
                                    <a href="#step-3" id="s-4" class="btn bunderan btn-default btn-circle disable" style="pointer-events: none">&nbsp;</a>
                                    <p class="ket my-0 text-secondary">Publish</p>
                                </div>
                            </div>
                        @elseif($proj->flag_mcs == 5)
                            <div class="stepwizard-row setup-panel">
                                <div class="stepwizard-step s2 stepwizard-step-success">
                                    <a href="#step-1" id="s-1" class="btn bunderan btn-success btn-circle reguler">&nbsp;</a>
                                    <p class="ket my-0 text-secondary">Checker</p>
                                </div>
                                <div class="stepwizard-step s3 stepwizard-step-success">
                                    <a href="#step-2" id="s-2" class="btn bunderan btn-success btn-circle reguler" style="pointer-events: none">&nbsp;</a>
                                    <p class="ket my-0 text-secondary">Signer</p>
                                </div>
                                <div class="stepwizard-step s4 stepwizard-step-success">
                                    <a href="#step-3" id="s-3" class="btn bunderan btn-success btn-circle reguler" style="pointer-events: none">&nbsp;</a>
                                    <p class="ket my-0 text-secondary">Admin</p>
                                </div>
                                <div class="stepwizard-step s5">
                                    <a href="#step-3" id="s-4" class="btn bunderan btn-success btn-circle reguler" style="pointer-events: none">&nbsp;</a>
                                    <p class="ket my-0 text-secondary">Publish</p>
                                </div>
                            </div>
                        @elseif($proj->flag_mcs == 6)
                            <div class="stepwizard-row setup-panel">
                                <div class="stepwizard-step s2 stepwizard-step-success">
                                    <a href="#step-1" id="s-1" class="btn bunderan btn-success btn-circle reguler">&nbsp;</a>
                                    <p class="ket my-0 text-secondary">Checker</p>
                                </div>
                                <div class="stepwizard-step s3 stepwizard-step-success">
                                    <a href="#step-2" id="s-2" class="btn bunderan btn-success btn-circle reguler" style="pointer-events: none">&nbsp;</a>
                                    <p class="ket my-0 text-secondary">Signer</p>
                                </div>
                                <div class="stepwizard-step s4 stepwizard-step-secondary">
                                    <a href="#step-3" id="s-3" class="btn bunderan btn-success btn-circle reguler" style="pointer-events: none">&nbsp;</a>
                                    <p class="ket my-0 text-secondary">Admin</p>
                                </div>
                                <div class="stepwizard-step s5">
                                    <a href="#step-3" id="s-4" class="btn bunderan btn-secondary btn-circle reguler" style="pointer-events: none">&nbsp;</a>
                                    <p class="ket my-0 text-secondary">Publish</p>
                                </div>
                            </div>
                        @endif
                    </div>
                    <hr class="my-1">
                    <div class="d-flex justify-content-between">
                        <div class="f-60 text-secondary">
                        @if($proj->flag_mcs == 2 || $proj->flag_mcs == 3 || $proj->flag_mcs == 4 || $proj->flag_mcs == 5 || $proj->flag_mcs == 6)
                            {{\Carbon\carbon::create($proj->checker_at)->format('d F Y, H.i') ?? '-'}}
                        @else
                            {{"-"}}
                        @endif
                        </div>
                        <div class="font-weight-bolder f-60">
                        Checker
                        </div>
                    </div>
                    <hr class="my-1">
                    <div class="d-flex justify-content-between">
                        <div class="f-60 text-secondary">
                        @if($proj->flag_mcs == 3 || $proj->flag_mcs == 4 || $proj->flag_mcs == 5 || $proj->flag_mcs == 6)
                            {{\Carbon\carbon::create($proj->signer_at)->format('d F Y, H.i') ?? '-'}}
                        @else
                            {{"-"}}
                        @endif
                        </div>
                        <div class="font-weight-bolder f-60">
                        Signer
                        </div>
                    </div>
                    <hr class="my-1">
                    <div class="d-flex justify-content-between">
                        <div class="f-60 text-secondary">
                        @if($proj->flag_mcs == 4 || $proj->flag_mcs == 5 || $proj->flag_mcs == 6)
                            {{\Carbon\carbon::create($proj->review_at)->format('d F Y, H.i') ?? '-'}}
                        @else
                            {{"-"}}
                        @endif
                        </div>
                        <div class="font-weight-bolder f-60">
                        Admin
                        </div>
                    </div>
                    <hr class="my-1">
                    <div class="d-flex justify-content-between">
                        <div class="f-60 text-secondary">
                        @if($proj->flag_mcs == 5 || $proj->flag_mcs == 6)
                            {{\Carbon\carbon::create($proj->publish_at)->format('d F Y, H.i') ?? '-'}}
                        @else
                            {{"-"}}
                        @endif
                        </div>
                        <div class="font-weight-bolder f-60">
                        Publish
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    <!-- MODAL LOG STATUS -->
@empty
@endforelse