class A():
    def __init__(self, umur, name='Dudung'):
        self.name = name
        self.umur = umur
        print self.name
        print self.umur

    def tulis(self):
        print 'tulis'

a = A(12)
a.tulis()
