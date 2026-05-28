import pytest
import time
from datetime import date, timedelta
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.service import Service
from webdriver_manager.chrome import ChromeDriverManager
import os

#config. navegador
@pytest.fixture
def driver():
    """Abre navegador, carga el formulario y lo cierra al terminar cada test."""
    nav = webdriver.Chrome(service=Service(ChromeDriverManager().install()))

    carpeta_actual = os.path.dirname(os.path.abspath(__file__))

    ruta_nueva = os.path.join(carpeta_actual, "..", "Formulario", "Index.html")

    ruta_absoluta = os.path.abspath(ruta_nueva)

    nav.get(f"file:///{ruta_absoluta}")
    
    yield nav 
    nav.quit()

# ==========================================
# INICIO TESTS PARA EL FORMULARIO
# ==========================================

def test_1_registro_con_descripcion(driver):
    """Req 1: Se ingresan todos los datos válidos y la descripción."""
    # Llenamos todos los campos
    driver.find_element(By.ID, "funcionario").send_keys("Diego P")
    driver.find_element(By.ID, "alumnos").send_keys("Juan Pérez")
    #formato DD/MM/AAAA
    driver.find_element(By.ID, "fecha").send_keys("10-10-2025") 
    driver.find_element(By.ID, "descripcion").send_keys("El alumno Juan Pérez insultó al profesor jefe en la sala.")
    
    driver.find_element(By.ID, "btn-submit").click()
    time.sleep(1)
    
    #se muestra el mensaje de éxito
    mensaje_exito = driver.find_element(By.ID, "msg-success")
    assert mensaje_exito.is_displayed()
    


def test_2_registro_sin_descripcion(driver):
    """Req 2: Registro exitoso sin descripción (opcional)."""
    # Llenamos los campos obligatorios solamente
    driver.find_element(By.ID, "funcionario").send_keys("María C")
    driver.find_element(By.ID, "alumnos").send_keys("Ana Silva")
    driver.find_element(By.ID, "fecha").send_keys("15-05-2025")
    # NO llenamos el campo de la descripción
    
    driver.find_element(By.ID, "btn-submit").click()
    time.sleep(1)
    
    # dado q la descripción debe ser opcional, debería mostrar éxito
    mensaje_exito = driver.find_element(By.ID, "msg-success")
    assert mensaje_exito.is_displayed()


def test_3_falta_campos_obligatorios(driver):
    """Req 3: Registro falla al faltar campos obligatorios"""
    # Llenamos alumnos y fecha, pero dejamos un campo obligatorio vacío... en este caso el del funcionario.
    driver.find_element(By.ID, "alumnos").send_keys("Carlos Sandoval")
    driver.find_element(By.ID, "fecha").send_keys("20-08-2025")
    driver.find_element(By.ID, "descripcion").send_keys("Discusión en el patio por un juego.")
    
    driver.find_element(By.ID, "btn-submit").click()
    time.sleep(1)
    
    mensaje_error = driver.find_element(By.ID, "msg-error")
    assert mensaje_error.is_displayed()


def test_4_fecha_posterior_a_la_actual(driver):
    """Req 4: Registro falla si la fecha del incidente es en el futuro."""

    driver.find_element(By.ID, "funcionario").send_keys("José M")
    driver.find_element(By.ID, "alumnos").send_keys("Pedro Gómez")
    driver.find_element(By.ID, "descripcion").send_keys("Atraso reiterado para entrar a la primera hora de clases.")
    
    #Generamos una fecha futura a la actual
    fecha_sigte_dia = (date.today() + timedelta(days=1)).strftime("%d-%m-%Y")
    driver.find_element(By.ID, "fecha").send_keys(fecha_sigte_dia)
    
    driver.find_element(By.ID, "btn-submit").click()
    time.sleep(1)
    
    #Debe mostrarse un mensaje de error porque no se aceptan fechas futuras
    mensaje_error = driver.find_element(By.ID, "msg-error")
    assert mensaje_error.is_displayed()