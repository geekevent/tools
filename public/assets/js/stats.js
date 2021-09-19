function add(path, value, space) {
    let xhttp =  new XMLHttpRequest();
    xhttp.open('POST', path)
    xhttp.setRequestHeader('Content-Type', 'application/json')
    xhttp.send(
        JSON.stringify({
            value: value,
            space: space
        })
    )

    xhttp.onreadystatechange = function () {
        if (4 === this.readyState) {
            let gauge = document.getElementById('gauge');
            let result = JSON.parse(this.response)
            gauge.innerText = result.gauge;
            let gaugeMax = parseInt(document.getElementById('gaugeMax').innerText);
            gauge.classList.remove('text-info', 'text-warning', 'text-danger')

            if (gaugeMax <= parseInt(result.gauge)) {
                gauge.classList.add('text-danger')
                return;
            }
            if (100 > gaugeMax - parseInt(result.gauge)) {
                gauge.classList.add('text-warning')
                return;
            }
            gauge.classList.add('text-info')
        }
    }

    return false;
}