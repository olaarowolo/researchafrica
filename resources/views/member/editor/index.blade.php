@extends('layouts.profile')


@section('page-name', 'Editor Page')

@section('content')

<div class="card shadow">
    <!-- <div class="card-header">
      <ul class="d-flex flex-column flex-md-row align-items-center ms-sm-4 justify-content-md-between links">
          <li class=" mt-md-3 typeTab" name="#tab1" > <a href="" class="text-dark" style="font-size: medium;">Article Under Review</a></li>
          <li class=" mt-md-3"> <a href="" class="text-dark typeTab" name="#tab2" style="font-size: medium;">Accepted Article</a></li>
          <li class=" mt-md-3" > <a href="" class="text-dark typeTab" name="#tab3" style="font-size: medium;"></a></li>
          <li class=" mt-md-3"> <a href="" class="text-dark typeTab" name="#tab4" style="font-size: medium;">Article History</a></li>
      </ul>
    </div> -->

    <div class="card-body px-4 mt-4">
        <!-- Tabs navs -->
        <ul class="nav nav-tabs  mb-3" id="ex1" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link active text-dark " id="ex2-tab-1" data-mdb-toggle="tab" href="#ex2-tabs-1" role="tab"
                    aria-controls="ex2-tabs-1" aria-selected="true">
                    <span>New Article</span> <span class="badge badge-success">2</span>
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link text-dark " id="ex2-tab-2" data-mdb-toggle="tab" href="#ex2-tabs-2" role="tab"
                    aria-controls="ex2-tabs-2" aria-selected="false">
                    Reviews Comment
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link text-dark " id="ex2-tab-3" data-mdb-toggle="tab" href="#ex2-tabs-3" role="tab"
                    aria-controls="ex2-tabs-3" aria-selected="false">
                    Articles on Review
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link text-dark " id="ex2-tab-4" data-mdb-toggle="tab" href="#ex2-tabs-4" role="tab"
                    aria-controls="ex2-tabs-4" aria-selected="false">
                    Corrected Articles
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link text-dark" id="ex2-tab-5" data-mdb-toggle="tab" href="#ex2-tabs-5" role="tab"
                    aria-controls="ex2-tabs-5" aria-selected="false">
                    Accepted Article
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link text-dark" id="ex2-tab-6" data-mdb-toggle="tab" href="#ex2-tabs-6" role="tab"
                    aria-controls="ex2-tabs-6" aria-selected="false">
                    Rejected Article
                </a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link text-dark" id="ex2-tab-7" data-mdb-toggle="tab" href="#ex2-tabs-7" role="tab"
                    aria-controls="ex2-tabs-7" aria-selected="false">
                    Article History
                </a>
            </li>
        </ul>
        <!-- Tabs navs -->

        <!-- Tabs content -->
        <div class="tab-content" id="ex2-content">
            <div class="tab-pane fade show active" id="ex2-tabs-1" role="tabpanel" aria-labelledby="ex2-tab-1">
                <p class="d-flex justify-content-between"><span><i class="fa-sharp fa-solid fa-calendar-days"></i> Jun
                        27, 2018 11:30 | <i class="fa-sharp fa-solid fa-folders"></i> Category: Health | <i
                            class="fa-sharp fa-solid fa-bookmark"></i> ID:QWETH678 | <i
                            class="fa-sharp fa-solid fa-book-bookmark"></i> 7th Edition </span>
                    <span><a href="#" type="button" class="btn btn-primary" style="height: 40px; width: 180px;">
                            Send For review
                        </a></span>
                </p>
                <p>Four ways to manage your health</p>

                <hr>

                <p class="d-flex justify-content-between"><span><i class="fa-sharp fa-solid fa-calendar-days"></i> Jun
                        27, 2018 11:30 | <i class="fa-sharp fa-solid fa-folders"></i> Category: Health | <i
                            class="fa-sharp fa-solid fa-bookmark"></i> ID:QWETH678 | <i
                            class="fa-sharp fa-solid fa-book-bookmark"></i> 7th Edition </span>
                    <span><a href="#" type="button" class="btn btn-primary" style="height: 40px; width: 180px;">
                            Send for review
                        </a></span>
                </p>
                <p>Four ways to manage your health</p>

                <hr>

                <p class="d-flex justify-content-between"><span><i class="fa-sharp fa-solid fa-calendar-days"></i> Jun
                        27, 2018 11:30 | <i class="fa-sharp fa-solid fa-folders"></i> Category: Health | <i
                            class="fa-sharp fa-solid fa-bookmark"></i> ID:QWETH678 | <i
                            class="fa-sharp fa-solid fa-book-bookmark"></i> 7th Edition </span>
                    <span><a href="#" type="button" class="btn btn-primary" style="height: 40px; width: 180px;">
                            Send for review
                        </a></span>
                </p>
                <p>Four ways to manage your health</p>
            </div>



            <div class="tab-pane fade" id="ex2-tabs-2" role="tabpanel" aria-labelledby="ex2-tab-2">
                <p class="d-flex justify-content-between"><span><i class="fa-sharp fa-solid fa-calendar-days"></i> Jun
                        27, 2018 11:30 | <i class="fa-sharp fa-solid fa-folders"></i> Category: Health | <i
                            class="fa-sharp fa-solid fa-bookmark"></i> ID:QWETH678 | <i
                            class="fa-sharp fa-solid fa-book-bookmark"></i> 7th Edition </span>
                    <span><a href="review_comment.html" class="btn btn-dark" style="height: 40px; width: 180px;">View
                            Comment</a></span>
                </p>

                <p>Four ways to manage your health</p>
                <hr>

                <p class="d-flex justify-content-between"><span><i class="fa-sharp fa-solid fa-calendar-days"></i> Jun
                        27, 2018 11:30 | <i class="fa-sharp fa-solid fa-folders"></i> Category: Health | <i
                            class="fa-sharp fa-solid fa-bookmark"></i> ID:QWETH678 | <i
                            class="fa-sharp fa-solid fa-book-bookmark"></i> 7th Edition </span>
                    <span><a href="review_comment.html" class="btn btn-dark" style="height: 40px; width: 180px;">View
                            Comment</a></span>
                </p>
                <p>Four ways to manage your health</p>



                <hr>

                <p class="d-flex justify-content-between"><span><i class="fa-sharp fa-solid fa-calendar-days"></i> Jun
                        27, 2018 11:30 | <i class="fa-sharp fa-solid fa-folders"></i> Category: Health | <i
                            class="fa-sharp fa-solid fa-bookmark"></i> ID:QWETH678 | <i
                            class="fa-sharp fa-solid fa-book-bookmark"></i> 7th Edition </span>
                    <span><a href="review_comment.html" class="btn btn-dark" style="height: 40px; width: 180px;">View
                            Comment</a></span>
                </p>
                <p>Four ways to manage your health</p>


            </div>


            <div class="tab-pane fade" id="ex2-tabs-3" role="tabpanel" aria-labelledby="ex2-tab-3">
                <p><i class="fa-sharp fa-solid fa-calendar-days"></i> Jun 27, 2018 11:30 | <i
                        class="fa-sharp fa-solid fa-folders"></i> Category: Health | <i
                        class="fa-sharp fa-solid fa-bookmark"></i> ID:QWETH678 | <i
                        class="fa-sharp fa-solid fa-book-bookmark"></i> 7th Edition</p>
                <p>Four ways to manage your health</p>

                <div class="justify-content-end">
                    <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#exampleModalLong"
                        style="height: 40px; width: 100px;">Comment</button>
                    <button class="btn btn-sm btn-success" style="height: 40px; width: 100px;">Accept</button>

                </div>

                <hr>

                <p><i class="fa-sharp fa-solid fa-calendar-days"></i> Jun 27, 2018 11:30 | <i
                        class="fa-sharp fa-solid fa-folders"></i> Category: Health | <i
                        class="fa-sharp fa-solid fa-bookmark"></i> ID:QWETH678 | <i
                        class="fa-sharp fa-solid fa-book-bookmark"></i> 7th Edition</p>
                <p>Four ways to manage your health</p>

                <div class="justify-content-end">
                    <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#exampleModalLong"
                        style="height: 40px; width: 100px;">Comment</button>
                    <button class="btn btn-sm btn-success" style="height: 40px; width: 100px;">Accept</button>

                </div>

                <hr>

                <p><i class="fa-sharp fa-solid fa-calendar-days"></i> Jun 27, 2018 11:30 | <i
                        class="fa-sharp fa-solid fa-folders"></i> Category: Health | <i
                        class="fa-sharp fa-solid fa-bookmark"></i> ID:QWETH678 | <i
                        class="fa-sharp fa-solid fa-book-bookmark"></i> 7th Edition</p>
                <p>Four ways to manage your health</p>

                <div class="justify-content-end">
                    <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#exampleModalLong"
                        style="height: 40px; width: 100px;">Comment</button>
                    <button class="btn btn-sm btn-success" style="height: 40px; width: 100px;">Accept</button>

                </div>
            </div>



            <div class="tab-pane fade" id="ex2-tabs-4" role="tabpanel" aria-labelledby="ex2-tab-4">
                <p><i class="fa-sharp fa-solid fa-calendar-days"></i> Jun 27, 2018 11:30 | <i
                        class="fa-sharp fa-solid fa-folders"></i> Category: Health | <i
                        class="fa-sharp fa-solid fa-bookmark"></i> ID:QWETH678 | <i
                        class="fa-sharp fa-solid fa-book-bookmark"></i> 7th Edition</p>
                <p>Four ways to manage your health</p>

                <div class="justify-content-end">
                    <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#exampleModalLong"
                        style="height: 40px; width: 100px;">Comment</button>
                    <button class="btn btn-sm btn-success" style="height: 40px; width: 100px;">Accept</button>
                    <button class="btn btn-sm btn-danger" style="height: 40px; width: 100px;">Reject</button>
                </div>

                <hr>

                <p><i class="fa-sharp fa-solid fa-calendar-days"></i> Jun 27, 2018 11:30 | <i
                        class="fa-sharp fa-solid fa-folders"></i> Category: Health | <i
                        class="fa-sharp fa-solid fa-bookmark"></i> ID:QWETH678 | <i
                        class="fa-sharp fa-solid fa-book-bookmark"></i> 7th Edition</p>
                <p>Four ways to manage your health</p>

                <div class="justify-content-end">
                    <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#exampleModalLong"
                        style="height: 40px; width: 100px;">Comment</button>
                    <button class="btn btn-sm btn-success" style="height: 40px; width: 100px;">Accept</button>
                    <button class="btn btn-sm btn-danger" style="height: 40px; width: 100px;">Reject</button>
                </div>

                <hr>

                <p><i class="fa-sharp fa-solid fa-calendar-days"></i> Jun 27, 2018 11:30 | <i
                        class="fa-sharp fa-solid fa-folders"></i> Category: Health | <i
                        class="fa-sharp fa-solid fa-bookmark"></i> ID:QWETH678 | <i
                        class="fa-sharp fa-solid fa-book-bookmark"></i> 7th Edition</p>
                <p>Four ways to manage your health</p>

                <div class="justify-content-end">
                    <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#exampleModalLong"
                        style="height: 40px; width: 100px;">Comment</button>
                    <button class="btn btn-sm btn-success" style="height: 40px; width: 100px;">Accept</button>
                    <button class="btn btn-sm btn-danger" style="height: 40px; width: 100px;">Reject</button>
                </div>
            </div>


            <div class="tab-pane fade" id="ex2-tabs-5" role="tabpanel" aria-labelledby="ex2-tab-5">
                <p><i class="fa-sharp fa-solid fa-calendar-days"></i> Jun 27, 2018 11:30 | <i
                        class="fa-sharp fa-solid fa-folders"></i> Category: Health | <i
                        class="fa-sharp fa-solid fa-bookmark"></i> ID:QWETH678 | <i
                        class="fa-sharp fa-solid fa-book-bookmark"></i> 7th Edition</p>
                <p>Four ways to manage your health</p>

                <hr>

                <p><i class="fa-sharp fa-solid fa-calendar-days"></i> Jun 27, 2018 11:30 | <i
                        class="fa-sharp fa-solid fa-folders"></i> Category: Health | <i
                        class="fa-sharp fa-solid fa-bookmark"></i> ID:QWETH678 | <i
                        class="fa-sharp fa-solid fa-book-bookmark"></i> 7th Edition</p>
                <p>Four ways to manage your health</p>

                <hr>

                <p><i class="fa-sharp fa-solid fa-calendar-days"></i> Jun 27, 2018 11:30 | <i
                        class="fa-sharp fa-solid fa-folders"></i> Category: Health | <i
                        class="fa-sharp fa-solid fa-bookmark"></i> ID:QWETH678 | <i
                        class="fa-sharp fa-solid fa-book-bookmark"></i> 7th Edition</p>
                <p>Four ways to manage your health</p>
            </div>


            <div class="tab-pane fade" id="ex2-tabs-6" role="tabpanel" aria-labelledby="ex2-tab-6">
                <p class="d-flex justify-content-between"><span><i class="fa-sharp fa-solid fa-calendar-days"></i> Jun
                        27, 2018 11:30 | <i class="fa-sharp fa-solid fa-folders"></i> Category: Health | <i
                            class="fa-sharp fa-solid fa-bookmark"></i> ID:QWETH678 | <i
                            class="fa-sharp fa-solid fa-book-bookmark"></i> 7th Edition </span>

                </p>
                <p>Four ways to manage your health</p>

                <hr>

                <p class="d-flex justify-content-between"><span><i class="fa-sharp fa-solid fa-calendar-days"></i> Jun
                        27, 2018 11:30 | <i class="fa-sharp fa-solid fa-folders"></i> Category: Health | <i
                            class="fa-sharp fa-solid fa-bookmark"></i> ID:QWETH678 | <i
                            class="fa-sharp fa-solid fa-book-bookmark"></i> 7th Edition </span>

                </p>
                <p>Four ways to manage your health</p>

                <hr>

                <p class="d-flex justify-content-between"><span><i class="fa-sharp fa-solid fa-calendar-days"></i> Jun
                        27, 2018 11:30 | <i class="fa-sharp fa-solid fa-folders"></i> Category: Health | <i
                            class="fa-sharp fa-solid fa-bookmark"></i> ID:QWETH678 | <i
                            class="fa-sharp fa-solid fa-book-bookmark"></i> 7th Edition </span>

                </p>
                <p>Four ways to manage your health</p>
            </div>


            <div class="tab-pane fade" id="ex2-tabs-7" role="tabpanel" aria-labelledby="ex2-tab-7">
                <p class="d-flex justify-content-between"><span><i class="fa-sharp fa-solid fa-calendar-days"></i> Jun
                        27, 2018 11:30 | <i class="fa-sharp fa-solid fa-folders"></i> Category: Health | <i
                            class="fa-sharp fa-solid fa-bookmark"></i> ID:QWETH678 | <i
                            class="fa-sharp fa-solid fa-book-bookmark"></i> 7th Edition </span>
                    <span><button class="btn btn-small btn-success"
                            style="height: 40px; width: 180px;">Status:Accepted</button></span>
                </p>
                <p>Four ways to manage your health</p>

                <hr>

                <p class="d-flex justify-content-between"><span><i class="fa-sharp fa-solid fa-calendar-days"></i> Jun
                        27, 2018 11:30 | <i class="fa-sharp fa-solid fa-folders"></i> Category: Health | <i
                            class="fa-sharp fa-solid fa-bookmark"></i> ID:QWETH678 | <i
                            class="fa-sharp fa-solid fa-book-bookmark"></i> 7th Edition </span>
                    <span><button class="btn btn-small btn-danger"
                            style="height: 40px; width: 180px;">Status:Rejected</button></span>
                </p>
                <p>Four ways to manage your health</p>

                <hr>

                <p class="d-flex justify-content-between"><span><i class="fa-sharp fa-solid fa-calendar-days"></i> Jun
                        27, 2018 11:30 | <i class="fa-sharp fa-solid fa-folders"></i> Category: Health | <i
                            class="fa-sharp fa-solid fa-bookmark"></i> ID:QWETH678 | <i
                            class="fa-sharp fa-solid fa-book-bookmark"></i> 7th Edition </span>
                    <span><button type="button" class="btn btn-small btn-success"
                            style="height: 40px; width: 180px;">Status:Accepted</button></span>
                </p>
                <p>Four ways to manage your health</p>
            </div>
        </div>
        <!-- Tabs content -->

    </div>
</div>


<!-- Modal -->
<div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">Comment</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <textarea class="required form-control" id="template-contactform-message"
                    name="template-contactform-message" rows="10" cols="30"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"
                    style="height: 40px; width: 180px;">Close</button>
                <button type="button" class="btn btn-primary" style="height: 40px; width: 180px;">Save changes</button>
            </div>
        </div>
    </div>
</div>
@endsection