<div class="input-div">
    <label>Nama</label>
    <input type="text" name="company_name[]" required placeholder="Nama Perusahaan">
</div>
<div class="input-div">
    <label>Jabatan</label>
    <input type="text" name="role[]" required placeholder="Jabatan Perusahaan">
</div>
<div class="input-div">
    <label>Lokasi</label>
    <input type="text" name="company_location[]" required placeholder="Lokasi Perusahaan">
</div>
<div class="input-div">
    <label>Masuk</label>
    <input type="month" name="start_date[]" required placeholder="Tahun Masuk">
</div>
<div class="input-div">
    <label>Keluar</label>
    <input type="month" name="end_date[]" required placeholder="Tahun Lulus">
</div>
<div class="input-div">
    <label>Action</label>
    @if ($canRemove)
        <button type="button" class="button-23 remove-work-btn">Remove</button>
    @else
        <button type="button" class="button-22 add-work-btn">Add More</button>
    @endif
</div>
