function updateMidCategories(tcat_id) {
    console.log('Selected Top Level Category ID:', tcat_id);

    fetch(`get-category.php?tcat_id=${tcat_id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.text();
        })
        .then(data => {
            document.getElementById('mcat_id').innerHTML = data;
            document.getElementById('ecat_id').innerHTML = '<option value="">Select End Level Category</option>'; // Reset end categories
        })
        .catch(error => console.error('Error fetching mid categories:', error));
}

function updateEndCategories(mcat_id) {
    console.log('Selected Mid Level Category ID:', mcat_id);

    fetch(`get-category.php?mcat_id=${mcat_id}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok ' + response.statusText);
            }
            return response.text();
        })
        .then(data => {
            document.getElementById('ecat_id').innerHTML = data;
        })
        .catch(error => console.error('Error fetching end categories:', error));
}
