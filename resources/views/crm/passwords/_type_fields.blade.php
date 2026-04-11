@props(['password' => null, 'type' => 'general'])

@switch($type)
  @case('sftp')
    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">Host <span class="text-danger">*</span></label>
        <input type="text" name="sftp_host" value="{{ $password?->sftp_host ?? old('sftp_host') }}" class="form-control">
        @error('sftp_host') <div class="text-danger text-xs mt-1">{{ $message }}</div> @enderror
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">Port</label>
        <input type="number" name="sftp_port" value="{{ $password?->sftp_port ?? old('sftp_port', 22) }}" class="form-control" min="1" max="65535">
        @error('sftp_port') <div class="text-danger text-xs mt-1">{{ $message }}</div> @enderror
      </div>
      <div class="col-12 mb-3">
        <label class="form-label">Cesta</label>
        <input type="text" name="sftp_path" value="{{ $password?->sftp_path ?? old('sftp_path') }}" class="form-control" placeholder="/home/user/">
        @error('sftp_path') <div class="text-danger text-xs mt-1">{{ $message }}</div> @enderror
      </div>
    </div>
  @break
  
  @case('hosting')
    <div class="row">
      <div class="col-md-6 mb-3">
        <label class="form-label">Poskytovatel</label>
        <input type="text" name="hosting_provider" value="{{ $password?->hosting_provider ?? old('hosting_provider') }}" class="form-control" placeholder="Např. WEDOS, Forpsi...">
        @error('hosting_provider') <div class="text-danger text-xs mt-1">{{ $message }}</div> @enderror
      </div>
      <div class="col-md-6 mb-3">
        <label class="form-label">FTP Host</label>
        <input type="text" name="ftp_host" value="{{ $password?->ftp_host ?? old('ftp_host') }}" class="form-control" placeholder="ftp.example.com">
        @error('ftp_host') <div class="text-danger text-xs mt-1">{{ $message }}</div> @enderror
      </div>
    </div>
  @break
  
  @case('admin')
    {{-- Admin je stejný jako general, jen s URL --}}
  @default
    {{-- General - bez specifických polí --}}
@endswitch
