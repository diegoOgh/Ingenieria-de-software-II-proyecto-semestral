import time

def log(actor, mensaje):
    print(f"  [{actor}] {mensaje}")
    time.sleep(0.05)


def pausa():
    input("\n  Siguiente incidente... \n")


class Funcionario:
    def registrar_incidente(self, alumno, tipo):
        log("Funcionario", f"Registra incidente: {alumno} — {tipo}")

class Encargado:
    def cambiar_estado(self, id_c, estado):
        log("Encargado", f"Cambia estado del incidente #{id_c} a {estado}")

    def agregar_nota(self, id_c, nota):
        log("Encargado", f"Agrega nota en #{id_c}: {nota}")

    def agregar_evidencia(self, id_c, descripcion):
        log("Encargado", f"Sube evidencia en #{id_c}: {descripcion}")

    def generar_pdf(self, id_c):
        log("Encargado", f"Genera ficha PDF del conflicto #{id_c}")

class Sistema:
    def guardar(self, id_c, alumno):
        log("Sistema", f"Guarda incidente #{id_c} — estado: PENDIENTE")

    def alerta(self, id_c):
        log("Sistema", f"Envía alerta al encargado — incidente #{id_c}")

    def cerrar(self, id_c, alumno):
        log("Sistema", f"Cierra incidente #{id_c} y registra en historial de {alumno}")


# ─────────────────────────────────────────────

def caso_1():
    f = Funcionario(); e = Encargado(); s = Sistema()

    f.registrar_incidente("Matías Rojas", "robo de celular durante el recreo")
    s.guardar(1, "Matías Rojas")
    s.alerta(1)
    e.cambiar_estado(1, "EN_PROCESO")
    e.agregar_nota(1, "Se revisaron camarines y patio, sin resultados")
    e.agregar_nota(1, "Matías recuerda haber estado en la biblioteca antes del recreo")
    e.agregar_nota(1, "Celular encontrado en la biblioteca, lo había dejado olvidado")
    e.cambiar_estado(1, "RESUELTO — sin terceros involucrados")
    s.cerrar(1, "Matías Rojas")

    print("\n  >> Resuelto: objeto extraviado, no hubo robo")


def caso_2():
    f = Funcionario(); e = Encargado(); s = Sistema()

    f.registrar_incidente("Benjamín Mora y Diego Fuentes", "pelea física en el pasillo")
    s.guardar(2, "Benjamín Mora")
    log("Sistema", "Detecta reincidencia de Diego Fuentes ⚠")
    s.alerta(2)
    e.cambiar_estado(2, "EN_PROCESO")
    e.agregar_evidencia(2, "Declaración escrita de testigo presencial")
    e.agregar_evidencia(2, "Registro de cámara del pasillo norte, 10:35 hrs")
    e.agregar_nota(2, "Benjamín presenta raspón en brazo derecho, se deriva a enfermería")
    e.agregar_nota(2, "Llamada a apoderado de Benjamín Mora — informado del incidente")
    e.agregar_nota(2, "Llamada a apoderado de Diego Fuentes — citado para reunión")
    e.agregar_nota(2, "Reunión con apoderados y alumnos realizada — ambas partes escuchadas")
    e.agregar_nota(2, "Mediación completada — alumnos se comprometen a no repetir conducta")
    e.generar_pdf(2)
    e.cambiar_estado(2, "RESUELTO")
    s.cerrar(2, "Benjamín Mora y Diego Fuentes")

    print("\n  >> Resuelto: mediación exitosa con participación de apoderados")


def caso_3():
    f = Funcionario(); e = Encargado(); s = Sistema()

    f.registrar_incidente("Valentina Soto", "burlas y exclusión reiterada por compañeras")
    s.guardar(3, "Valentina Soto")
    s.alerta(3)
    e.cambiar_estado(3, "EN_PROCESO")
    e.agregar_nota(3, "Entrevista con Valentina — situación ocurre hace 3 semanas")
    e.agregar_nota(3, "Entrevista con compañeras involucradas — reconocen la conducta")
    e.agregar_evidencia(3, "Capturas de mensajes de grupo compartidas por Valentina")
    e.agregar_nota(3, "Llamada a apoderada de Valentina — informada y agradece la gestión")
    e.agregar_nota(3, "Citación a apoderados de compañeras involucradas")
    e.agregar_nota(3, "Reunión conjunta realizada — acuerdo de convivencia firmado")
    e.agregar_nota(3, "Seguimiento programado para 2 semanas más")
    e.generar_pdf(3)
    e.cambiar_estado(3, "EN_SEGUIMIENTO")
    s.cerrar(3, "Valentina Soto")

    print("\n  >> En seguimiento: acuerdo alcanzado, revisión pendiente")


# ─────────────────────────────────────────────

print("\n  Sistema de Gestión de Convivencia Escolar — Simulación")

caso_1()
pausa()
caso_2()
pausa()
caso_3()

print("\n")
