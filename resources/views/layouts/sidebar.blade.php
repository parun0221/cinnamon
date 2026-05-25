
<div class="sidebar">
    <div class="filter-header">
        <span>Filter</span>
        <a href="#" class="reset-link">
            <img src="/images/reset.png" alt="Reset" class="reset-icon">
            <span>Reset</span>
        </a>
    </div>                
    <div class="search-box mt-3">
        <img src="/images/search.png" alt="Search" class="search-icon">
        <input type="text" placeholder="Cari berdasarkan Nama, Posisi..." id="search">
    </div>

    <div class="filter-section mt-4">
      <p class="filter-title">Skill</p>
      <select id="skills-select" name="skills[]" class="form-control" multiple required>
        @foreach($skills as $skill)
          <option value="{{ $skill->id }}">{{ $skill->name }}</option>
        @endforeach
      </select>
      <script>
          document.addEventListener('DOMContentLoaded', function() {
              new Choices('#skills-select', {
              removeItemButton: true,
              placeholderValue: 'Pilih skill...',
              searchEnabled: true,
            });
          });
      </script>
    </div>

    <div class="filter-section mt-4">
        <p class="filter-title">Pengalaman</p>
        <div class="salary-dropdown">
            <select id="experience">
                <option value="">Pilih Rentang Pengalaman</option>
                <option value="1">Kurang dari 1 tahun</option>
                <option value="3">1-3 tahun</option>
                <option value="5">4-5 tahun</option>
                <option value="7">Lebih dari 5 tahun</option>
            </select>
        </div>
    </div>

    <script>
      document.addEventListener('DOMContentLoaded', function () {
          const searchInput = document.querySelector('input[placeholder="Cari berdasarkan Nama, Posisi..."]');
          const expSelect = document.getElementById('experience');
          const skillCheckboxes = document.querySelectorAll('input[name="skills[]"]');
  
          function fetchData() {
              const skills = Array.from(skillCheckboxes)
                                  .filter(chk => chk.checked)
                                  .map(chk => chk.value);
  
              const search = searchInput.value;
              const experience = expSelect.value;
  
              const params = new URLSearchParams();
              if (search) params.append('search', search);
              if (experience) params.append('experience', experience);
              skills.forEach(skill => params.append('skills[]', skill));
  
              fetch(`/filter?${params.toString()}`)
                  .then(response => response.text())
                  .then(html => {
                      document.querySelector('#main-content').innerHTML = html;
                  });
          }
  
          searchInput.addEventListener('input', fetchData);
          expSelect.addEventListener('change', fetchData);
          skillCheckboxes.forEach(chk => chk.addEventListener('change', fetchData));
      });
  </script>
  
    
</div>