<script>
    
    function buildPlayersTable(jsonPath, containerId) {
      fetch(jsonPath)
        .then(response => {
          if (!response.ok) {
            throw new Error('Failed to fetch ' + jsonPath);
          }
          return response.json();
        })
        .then(data => {
          if (!Array.isArray(data) || data.length === 0) {
            throw new Error('No player data found');
          }

          const table = document.createElement('table');
          const thead = document.createElement('thead');
          const tbody = document.createElement('tbody');

          // Build table headers from keys
          const headers = Object.keys(data[0]);
          const headerRow = document.createElement('tr');
          headers.forEach(key => {
            const th = document.createElement('th');
            th.textContent = key;
            headerRow.appendChild(th);
          });
          thead.appendChild(headerRow);

          // Build table rows
          data.forEach(player => {
            const row = document.createElement('tr');
            headers.forEach(key => {
              const td = document.createElement('td');
              td.textContent = player[key];
              row.appendChild(td);
            });
            tbody.appendChild(row);
          });

          table.appendChild(thead);
          table.appendChild(tbody);

          const container = document.getElementById(containerId);
          container.innerHTML = ''; // Clear previous content
          container.appendChild(table);
        })
        .catch(error => {
          console.error(error);
          const container = document.getElementById(containerId);
          container.textContent = 'Error loading table.';
        });
    }
  </script>