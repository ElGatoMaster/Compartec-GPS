import numpy as np
import matplotlib.pyplot as plt

#Parametros
TD = 200                                        #Temperatura deseada
PMaxima = 100                                   #Potencia máxima del calentador
Ganancia = 0.5                                  #Ganancia proporcional del controlador

Duracion = 50                                   #Duración de la simulación en segundos
Intervalo = 0.1                                 #Intervalo de tiempo entre puntos de la simulación
Puntos = int(Duracion / Intervalo)              #Número de puntos en la simulación

t = np.linspace(0, Duracion, Puntos)
TempActual = np.zeros_like(t)
TempActual[0] = 25                              #Temperatura inicial del horno (25°C)

# Control proporcional
for i in range(1, Puntos):
    error = TD - TempActual[i - 1]
    Potencia = Ganancia * error
    Potencia = max(0, min(PMaxima, Potencia))   #Limitar la potencia
    TempActual[i] = TempActual[i - 1] + Potencia * Intervalo

# Visualización de resultados
plt.figure(figsize=(10, 6))
plt.plot(t, TempActual, label='Temperatura del horno')
plt.axhline(TD, color='r', linestyle='--', label='Temperatura deseada')
plt.xlabel('Tiempo (s)')
plt.ylabel('Temperatura (°C)')
plt.title('Control de Temperatura en un Horno')
plt.legend()
plt.grid(True)
plt.show()
