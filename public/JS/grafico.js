fetch("/PRIMER_PROYECTO_1/dashboard.php")
  .then((response) => response.json())
  .then((data) => {
    // Gráfico por área (departamento)
    const labelsArea = data.area.map((item) => item.nombre);
    const valoresArea = data.area.map((item) => parseInt(item.total));

    const ctxArea = document.getElementById("graficoArea").getContext("2d");
    new Chart(ctxArea, {
      type: "bar",
      data: {
        labels: labelsArea,
        datasets: [
          {
            label: "Empleados por Área",
            data: valoresArea,
            backgroundColor: "rgba(54, 162, 235, 0.3)",
            borderColor: "rgba(54, 162, 235, 1)",
            borderWidth: 1,
          },
        ],
      },
      options: { responsive: true },
    });

    // Gráfico por cargo
    const labelsCargo = data.cargo.map((item) => item.nombre);
    const valoresCargo = data.cargo.map((item) => parseInt(item.total));

    const ctxCargo = document.getElementById("graficoCargo").getContext("2d");
    new Chart(ctxCargo, {
      type: "bar",
      data: {
        labels: labelsCargo,
        datasets: [
          {
            label: "Empleados por Cargo",
            data: valoresCargo,
            backgroundColor: "rgba(255, 153, 0, 1)",
            borderColor: "rgba(217, 255, 4, 1)",
            borderWidth: 1,
          },
        ],
      },
      options: { responsive: true },
    });
  })
  .catch((error) => console.error("Error al cargar datos:", error));
