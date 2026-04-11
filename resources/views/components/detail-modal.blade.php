<!-- Generic detail modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content">
      <div id="modalContent">
        <!-- Content loaded via AJAX -->
        <div class="text-center p-5">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Načítání…</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
// Open modal with dynamic content
function openDetailModal(url) {
  const modal = new bootstrap.Modal(document.getElementById('detailModal'));
  const contentDiv = document.getElementById('modalContent');
  
  // Show loading spinner
  contentDiv.innerHTML = `
    <div class="text-center p-5">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Načítání…</span>
      </div>
    </div>
  `;
  
  modal.show();
  
  // Fetch content
  fetch(url)
    .then(response => response.text())
    .then(html => {
      contentDiv.innerHTML = html;
    })
    .catch(error => {
      contentDiv.innerHTML = '<div class="alert alert-danger m-3">Chyba při načítání detailů.</div>';
    });
}

// Listen for modal events for fullscreen navigation
document.getElementById('detailModal')?.addEventListener('show.bs.modal', function() {
  // This allows links inside modal to work normally
});
</script>
@endpush
