@php
  $selectedAssignees = session()->hasOldInput() ? old('assignees',[]) : ($task?->assignees->modelKeys() ?? []);
  $selectedAssignees = is_array($selectedAssignees) ? $selectedAssignees : [];
@endphp
<div class="form-grid">
  <div class="form-main">
    <x-form-field name="title" label="Název úkolu" :value="$task?->title" placeholder="Co je potřeba udělat?" required />
    <x-form-field name="project_id" label="Projekt" type="select" required>
      <option value="">Vyberte projekt</option>
      @foreach($projects as $project)<option value="{{ $project->id }}" @selected(old('project_id',$task?->project_id ?? $selectedProject?->id) == $project->id)>{{ $project->name }} — {{ $project->client->name }}</option>@endforeach
    </x-form-field>
    <x-form-field name="description" label="Zadání" type="textarea" rows="7" :value="$task?->description" inputClass="rich-editor-source" />
    <details class="attachment-picker"><summary>Přidat přílohy nebo screenshoty</summary><x-form-field name="attachments[]" label="Soubory" type="file" multiple accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip" /><p class="context-line">Nejvýše 8 souborů, každý do 10 MB.</p></details>
  </div>
  <aside class="form-settings">
    <h2>Nastavení úkolu</h2>
    <x-form-field name="status" label="Stav" type="select" required>@foreach(['todo'=>'K řešení','in_progress'=>'Probíhá','review'=>'Ke kontrole','done'=>'Dokončeno'] as $key=>$label)<option value="{{ $key }}" @selected(old('status',$task?->status ?? 'todo') === $key)>{{ $label }}</option>@endforeach</x-form-field>
    <x-form-field name="priority" label="Priorita" type="select" required>@foreach(['low'=>'Nízká','medium'=>'Střední','high'=>'Vysoká'] as $key=>$label)<option value="{{ $key }}" @selected(old('priority',$task?->priority ?? 'medium') === $key)>{{ $label }}</option>@endforeach</x-form-field>
    <x-form-field name="starts_at" label="Zahájení" type="date" :value="$task?->starts_at?->format('Y-m-d')" />
    <x-form-field name="due_at" label="Termín dokončení" type="date" :value="$task?->due_at?->format('Y-m-d')" />
    <fieldset class="form-section mb-0"><legend class="form-label">Kdo bude úkol řešit</legend><div class="people-options">@foreach($allUsers as $person)<label class="person-option"><input type="checkbox" name="assignees[]" value="{{ $person->id }}" @checked(in_array($person->id,$selectedAssignees))><span class="person-avatar">{{ mb_substr($person->name,0,1) }}</span><span>{{ $person->name }}</span></label>@endforeach</div>@error('assignees')<p class="text-danger">{{ $message }}</p>@enderror</fieldset>
  </aside>
</div>
