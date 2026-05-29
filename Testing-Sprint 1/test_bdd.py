import pytest
import mysql.connector
from mysql.connector import Error

def test_conexion_base_datos():
    #Se intenta conectar a la bdd local
    try:
        conexion = mysql.connector.connect(
            host="127.0.0.1",
            user="root",    
            database="gestion_escolar"
        )
        
        #Conexión exitosa = true
    except Error as e:
        # Si falla, muestra error
        pytest.fail(f"Fallo la conexión a la base de datos: {e}")
        
    finally:
        #Se cierra la conexión para no dejar procesos abiertos
        if 'conexion' in locals() and conexion.is_connected():
            conexion.close()

def test_insertar_alumno():
    try:
        conexion = mysql.connector.connect(
            host="127.0.0.1", user="root", password="", database="gestion_escolar"
        )
        cursor = conexion.cursor()
        
        # Preparamos un alumno de prueba
        sql = "INSERT INTO alumnos (nro_matricula, nombre, apellidos, curso) VALUES (%s, %s, %s, %s)"
        valores = (9999, "Jose", "Skpe", "Tercero Medio")
        
        cursor.execute(sql, valores)
        conexion.commit()
        
        # Comprobamos q se insertó 1 fila en realidad
        assert cursor.rowcount == 1
        
    except Error as e:
        pytest.fail(f"Fallo al insertar en la base de datos: {e}")
        
    finally:
        #El dato de prueba se borra par no ensuciar la bdd real.
        if 'conexion' in locals() and conexion.is_connected():
            cursor.execute("DELETE FROM alumnos WHERE nro_matricula = 9999")
            conexion.commit()
            cursor.close()
            conexion.close()

def test_llave_foranea_conflicto_funcionario():
    try:
        conexion = mysql.connector.connect(
            host="127.0.0.1", user="root", password="", database="gestion_conflictos"
        )
        cursor = conexion.cursor()
        
        # intentamos añadir conflicto a un funcionario q no existe.
        sql = "INSERT INTO casos_conflicto (id_funcionario_cargo) VALUES (%s)"
        valores = (999,)
        
        #usamos "pytest.raises" porque se espera que el caso falle.
        # Si la base de datos permite guardar esto, significa que la llave foránea no está siendo efectiva...
        with pytest.raises(Error) as error_info:
            cursor.execute(sql, valores)
            conexion.commit()
            
        #se imprime el error que dio Mysql para confirmar que fue por la llave foranea
        print(f"\nSe bloqueó correctamente la inserción: {error_info.value}")

    except Exception as e:
        pytest.fail(f"El test falló por un motivo inesperado: {e}")
        
    finally:
        if 'conexion' in locals() and conexion.is_connected():
            cursor.close()
            conexion.close()