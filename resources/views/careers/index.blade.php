<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $brand['name'] }} - Careers</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    @if ($turnstileSiteKey)
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endif
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ asset('assets/svg/svg-icons-animate.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="icon" href="{{ asset($brand['favicon'] ?? $brand['logo']) }}">
</head>

<body class="snippet-body">
    <div class="container">
        <div class="card">
            <div class="form">
                <div class="left-side">
                    <div class="left-heading">
                        <h3>{{ $brand['name'] }}</h3>
                    </div>
                    <div class="steps-content">
                        <h3>Step <span class="step-number">1</span></h3>
                        <p class="step-number-content active">Enter your personal information to get closer to companies.</p>
                        <p class="step-number-content d-none">Get to know better by adding your diploma, certificate and education life.</p>
                        <p class="step-number-content d-none">Help companies get to know you better by telling them about your past experiences.</p>
                        <p class="step-number-content d-none">Add your profile picture and let companies find you fast.</p>
                        <p class="step-number-content d-none">To continue, you must agree to the terms.</p>
                    </div>
                    <ul class="progress-bar">
                        <li class="active">Personal Information</li>
                        <li>Education</li>  
                        <li>Work Experience</li>
                        <li>User Photo</li>
                        <li>Agreements</li>
                    </ul>
                </div>
                <div class="right-side">
                    @if ($errors->any())
                        <div class="alert-container">
                            <div class="alert alert-danger" role="alert">
                                {{ $errors->first() }}
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('careers.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="brand_key" value="{{ $brand['key'] }}">

                        <div class="main active">
                            <a href="{{ $brand['website'] }}" target="_blank">
                                <img src="{{ asset($brand['logo']) }}" alt="{{ $brand['name'] }}" width="120">
                            </a>
                            <div class="text">
                                <h2>Your Personal Information</h2>
                                <p>Enter your personal information to get closer to company.</p>
                            </div>
                            <div class="input-text">
                                <div class="input-div">
                                    <label>Nama Lengkap</label>
                                    <input type="text" name="full_name" id="user_name" value="{{ old('full_name') }}" required placeholder="Nama Lengkap">
                                </div>
                                <div class="input-div">
                                    <label>Nama Panggilan</label>
                                    <input type="text" name="nickname" value="{{ old('nickname') }}" required placeholder="Nama Panggilan">
                                </div>
                            </div>
                            <div class="input-text">
                                <div class="input-div">
                                    <label>Tempat Lahir</label>
                                    <input type="text" name="pob" value="{{ old('pob') }}" required placeholder="Tempat Lahir">
                                </div>
                                <div class="input-div">
                                    <label>Tanggal Lahir</label>
                                    <input type="date" name="dob" value="{{ old('dob') }}" required>
                                </div>
                            </div>
                            <div class="input-text">
                                <div class="input-div">
                                    <label>Email</label>
                                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="name@example.com">
                                </div>
                                <div class="input-div">
                                    <label>Nomor Handphone</label>
                                    <input type="tel" name="phone" value="{{ old('phone') }}" required maxlength="13" pattern="[0-9]{1,13}" inputmode="numeric" autocomplete="tel" data-phone-input placeholder="Nomor Handphone">
                                </div>
                            </div>
                            <div class="input-text">
                                <div class="input-div">
                                    <label>Jenis Kelamin</label>
                                    <select name="gender" required>
                                        <option value="">Pilih Jenis Kelamin</option>
                                        @foreach ($genders as $gender)
                                            <option value="{{ $gender->id }}" @selected(old('gender') == $gender->id)>{{ $gender->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="input-div">
                                    <label>Status Pernikahan</label>
                                    <select name="marital_status" required>
                                        <option value="">Pilih Status</option>
                                        @foreach ($maritalStatuses as $status)
                                            <option value="{{ $status->id }}" @selected(old('marital_status') == $status->id)>{{ $status->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="input-text">
                                <div class="input-div">
                                    <label>Alamat Lengkap</label>
                                    <textarea name="address" rows="3" required placeholder="Alamat Lengkap">{{ old('address') }}</textarea>
                                </div>
                            </div>
                            <div class="buttons">
                                <button type="button" class="next_button">Next Step</button>
                            </div>
                        </div>

                        <div class="main">
                            <a href="{{ $brand['website'] }}" target="_blank">
                                <img src="{{ asset($brand['logo']) }}" alt="{{ $brand['name'] }}" width="120">
                            </a>
                            <div class="text">
                                <h2>Education</h2>
                                <p>Inform companies about your education life.</p>
                            </div>
                            <div id="show-input">
                                <div class="input-text">
                                    @include('careers.partials.education-fields', ['canRemove' => false])
                                </div>
                            </div>
                            <div class="buttons button_space">
                                <button type="button" class="back_button">Back</button>
                                <button type="button" class="next_button">Next Step</button>
                            </div>
                        </div>

                        <div class="main">
                            <a href="{{ $brand['website'] }}" target="_blank">
                                <img src="{{ asset($brand['logo']) }}" alt="{{ $brand['name'] }}" width="120">
                            </a>
                            <div class="text">
                                <h2>Work Experiences</h2>
                                <p>Can you talk about your past work experience?</p>
                            </div>
                            <div id="show-input-work">
                                <div class="input-text">
                                    @include('careers.partials.work-fields', ['canRemove' => false])
                                </div>
                            </div>
                            <div class="buttons button_space">
                                <button type="button" class="back_button">Back</button>
                                <button type="button" class="next_button">Next Step</button>
                            </div>
                        </div>

                        <div class="main">
                            <a href="{{ $brand['website'] }}" target="_blank">
                                <img src="{{ asset($brand['logo']) }}" alt="{{ $brand['name'] }}" width="120">
                            </a>
                            <div class="text">
                                <h2>User Photo</h2>
                                <p>Upload your profile picture and share yourself.</p>
                            </div>
                            <div class="input-text">
                                <div class="input-div">
                                    <label>Pekerjaan Dilamar</label>
                                    <select name="job_vacancy_id" @if ($jobVacancies->isNotEmpty()) required @endif>
                                        @if ($jobVacancies->isEmpty())
                                            <option value="">Belum ada lowongan aktif</option>
                                        @else
                                            <option value="">Pilih Pekerjaan</option>
                                            @foreach ($jobVacancies as $vacancy)
                                                <option value="{{ $vacancy->id }}" @selected(old('job_vacancy_id') === $vacancy->id)>{{ $vacancy->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                <div class="input-div">
                                    <label>Gaji yang diharapkan</label>
                                    <input type="text" name="expected_salary" value="{{ old('expected_salary') }}" required inputmode="numeric" autocomplete="off" data-currency-input placeholder="Rp 10.000.000">
                                </div>
                            </div>
                            <div class="input-text">
                                <div class="input-div">
                                    <label>Self Resume</label>
                                    <textarea name="self_resume" rows="3" required placeholder="Tuliskan ringkasan singkat tentang diri Anda">{{ old('self_resume') }}</textarea>
                                </div>
                            </div>
                            <div class="input-text">
                                <div class="input-div">
                                    <label>Link Portfolio</label>
                                    <input type="url" name="portfolio_web_address" value="{{ old('portfolio_web_address') }}" required pattern="https://.*" title="Gunakan URL dengan awalan https://" placeholder="https://portfolio.example.com">
                                </div>
                            </div>
                            <div class="input-text">
                                <div class="input-div">
                                    <label>Pas Photo (Max 2MB | JPG, PNG)</label><br><br>
                                    <img id="img-preview" src="{{ asset('assets/img/choose.png') }}" alt="" height="100">
                                    <div class="upload-control">
                                        <div id="file-chosen-img">No file chosen</div>
                                        <input name="photo" id="input-img" type="file" accept=".jpg,.jpeg,.png,image/jpeg,image/png" data-allowed-extensions="jpg,jpeg,png" required hidden>
                                        <label class="actual-btn-img" for="input-img">Choose File</label>
                                    </div>
                                </div>
                                <div class="input-div">
                                    <label>Curriculum Vitae (Max 2MB | PDF, DOC, DOCX)</label><br><br>
                                    <img id="doc-preview" src="{{ asset('assets/img/pdf-add.png') }}" alt="" height="100">
                                    <div class="upload-control">
                                        <div id="file-chosen-doc">No file chosen</div>
                                        <input name="cv" id="doc" type="file" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" data-allowed-extensions="pdf,doc,docx" required hidden>
                                        <label class="actual-btn-doc" for="doc">Choose File</label>
                                    </div>
                                </div>
                            </div>
                            <div class="buttons button_space">
                                <button type="button" class="back_button">Back</button>
                                <button type="button" class="next_button">Next Step</button>
                            </div>
                        </div>

                        <div class="main">
                            <a href="{{ $brand['website'] }}" target="_blank">
                                <img src="{{ asset($brand['logo']) }}" alt="{{ $brand['name'] }}" width="120">
                            </a>
                            <div class="text">
                                <h2>Agreements</h2>
                                <p>To continue, you must agree to the Term of Service and Privacy Policy.</p>
                            </div>
                            <div class="list_block">
                                <ul>
                                    @foreach ([
                                        1 => 'Saya TIDAK PERNAH diberhentikan dengan tidak hormat dari suatu jabatan pekerjaan atau pendidikan.',
                                        2 => 'Saya TIDAK PERNAH melakukan pelanggaran sehingga dihukum.',
                                        3 => 'Saya pada waktu ini TIDAK MENDERITA penyakit yang dapat membahayakan orang lain.',
                                        4 => 'Saya tidak pernah menjadi anggota dari OrPol/ OrMas / Partai Terlarang.',
                                        5 => 'Saya MENYETUJUI dan TUNDUK bahwa selama pendidikan atau bekerja di PT. Rajawali Nusantara Bersama, Saya TIDAK TERIKAT perjanjian atau kontrak kerja atau tidak mempunyai kewajiban atau masalah Ketenagakerjaan dengan perusahaan atau instansi lain, baik sekarang maupun pada masa yang akan datang.',
                                    ] as $value => $label)
                                        <li>
                                            <div class="checkbox_radio_container">
                                                <input type="checkbox" id="agreement_{{ $value }}" name="agreement[]" class="required" value="{{ $value }}" required>
                                                <label class="checkbox" for="agreement_{{ $value }}"></label>
                                                <label for="agreement_{{ $value }}" class="wrapper">{{ $label }}</label>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                            @if ($turnstileSiteKey)
                                <div class="turnstile-field">
                                    <div class="cf-turnstile" data-sitekey="{{ $turnstileSiteKey }}"></div>
                                </div>
                            @endif
                            <div class="buttons button_space">
                                <button type="button" class="back_button">Back</button>
                                <button type="submit">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <template id="education-template">
        <div class="input-text">
            @include('careers.partials.education-fields', ['canRemove' => true])
        </div>
    </template>

    <template id="work-template">
        <div class="input-text">
            @include('careers.partials.work-fields', ['canRemove' => true])
        </div>
    </template>

    <script src="{{ asset('assets/js/script.js') }}"></script>
</body>

</html>
