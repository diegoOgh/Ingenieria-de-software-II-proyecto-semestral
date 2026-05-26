##### **TEST PARA EL FORMULARIO**



###### 1.-Registro exitoso (añadiendo descripción)  



Se ingresan los alumnos involucrados, una fecha válida, el funcionario que anota el conflicto y la descripción  



###### 2.-Registro exitoso (sin añadir descripción) 



Se ingresan los alumnos involucrados, una fecha válida, el funcionario que anota el conflicto, pero en este caso no se añade la descripición dado que al hacer el diagrama de casos de uso habiamos quedado en que era opcional y se podía añadir más tarde. 



###### 3.-Registro con falla dado que faltan campos obligatorios. 



Se deja vacío algún campo necesario para guardar el registro del conflicto. -> Se espera que se muestre algún mensaje de error para el problema existente. 



###### 4.-Fecha de registro posterior a la fecha actual 



La fecha de registro de un conflicto no puede ser posterior a la fecha actual, por lo que en ese caso debería pedir nuevamente la fecha o mostrar un error para que el usuario la ingrese nuevamente. 



##### **TEST PARA LA BDD**



1.-La tabla en la cual están registrados debe aceptar que la descripción pueda ser un campo nulo dado que es opcional, pero campos como el de los alumnos o el de funcionario no pueden ser nulos. 


**TEST PARA EL BACKEND**
---


1.-Si el sistema recibe una entrada válida y tiene una bdd activa entonces debería retornar que un conflicto ha sido guardado con éxito. 



2.- Si se recibe un formulario sin alumnos y sin funcionario entonces se debe frenar la ejecución antes de llamar a la bdd e informar que la información es incompleta... 



3.-Si hay un error al conectar la bdd debería enviarse un error de sistema. 





##### **TEST VISUALIZACIÓN CONFLICTO**



1.-Garantizar que la consulta realizada a la bdd para obtener los datos contenga toda la información importante requerida.



2.-El conflicto debe mostrar una descripción en el caso de que la tenga. En el caso de que no, entonces se muestra un texto por defecto ej: “Sin descripción” 







