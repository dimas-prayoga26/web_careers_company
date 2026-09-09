<div class="input-div">
    <label>Jenjang</label>
    <select name="educational_level[]" required>
        <option value="">Pilih Jenjang</option>
        @foreach ($educationLevels as $level)
            <option value="{{ $level->id }}">{{ $level->name }}</option>
        @endforeach
    </select>
</div>
<div class="input-div">
    <label>Nama</label>
    <input type="text" name="educational_institution[]" required placeholder="Nama Perguruan Tinggi">
</div>
<div class="input-div">
    <label>Jurusan</label>
    <input type="text" name="department[]" required placeholder="Jurusan">
</div>
<div class="input-div">
    <label>IPK</label>
    <input type="text" name="gpa[]" required placeholder="IPK atau Nilai Ujian Nasional">
</div>
<div class="input-div">
    <label>Masuk</label>
    <input type="number" name="start_education[]" min="1900" max="2099" step="1" maxlength="4" required placeholder="Tahun Masuk">
</div>
<div class="input-div">
    <label>Lulus</label>
    <input type="number" name="graduate_education[]" min="1900" max="2099" step="1" maxlength="4" required placeholder="Tahun Lulus">
</div>
<div class="input-div">
    <label>Action</label>
    @if ($canRemove)
        <button type="button" class="button-23 remove-education-btn" style="margin-bottom: 15px;">Remove</button>
    @else
        <button type="button" class="button-22 add-education-btn">Add More</button>
    @endif
</div>
