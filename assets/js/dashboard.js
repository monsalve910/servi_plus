document.addEventListener("DOMContentLoaded", function () {
  const labelsDept = window.chartData ? window.chartData.labelsDept || [] : [];
  const dataDept = window.chartData ? window.chartData.dataDept || [] : [];
  const labelsCargo = window.chartData
    ? window.chartData.labelsCargo || []
    : [];
  const dataCargo = window.chartData ? window.chartData.dataCargo || [] : [];
  const ctxDept = document
    .getElementById("graficoDepartamento")
    .getContext("2d");
  new Chart(ctxDept, {
    type: "bar",
    data: {
      labels: labelsDept,
      datasets: [
        {
          label: "Empleados por Departamento",
          data: dataDept,
          backgroundColor: "rgba(54, 162, 235, 0.6)",
        },
      ],
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
    },
  });

  const ctxCargo = document.getElementById("graficoCargo").getContext("2d");
  new Chart(ctxCargo, {
    type: "bar",
    data: {
      labels: labelsCargo,
      datasets: [
        {
          label: "Empleados por Cargo",
          data: dataCargo,
          backgroundColor: "rgba(255, 99, 132, 0.6)",
        },
      ],
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
    },
  });
});
