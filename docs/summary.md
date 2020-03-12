# Preguntas utiles al comenzar

- Qué queremos desarrollar?
- Cuál es el primer feature?
- Cuál es el primer test?

Empieza creando un "outside in" Acceptance Test y lo pone en la carpeta Features.

Después explica AAA y en la etapa Arrange explica como se puede usar
"direct model access" para crear el concierto. Esto significa desde los tests
podemos acceder directamente a los objetos de dominio.

Mientras crea el concierto comenta sobre persistir dinero en centavos.

Al hacer la ruta no hace lo minimo, como un callback y listo, sino que lo manda a 
un controller. Hace alusion a que usa una estrategia llamada 
"Programming by wishful thinking" que trata sobre programar haciendo referencia
a cosas que todavia no existen pero que queres que exitan (metodos, clases, etc)

# Unit Testing Presentation Logic
Quiere llevar todo el formateo que pasa en la vista a un objeto dedicado.
Piensa usar un presenter. Pero no quiere agregar complejidad, asi que agrega el comportamiento
al modelo... mmmm. Algo como "formatedDate".
Aca empieza con un loop de TDD clasiste para agregar el comportamiento que necesita para
formatear.

Cuando hace el test y crea un concierto, solo está preocupado por la fecha,
agregaria ruido setear todo lo demás. Para esto usa model factories.


