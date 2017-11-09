import copy
import queue as Q

final_board_3 = [[1, 2], [3, 0]]
final_board_8 = [[1, 2, 3], [4, 5, 6], [7, 8, 0]]
final_board_15 = [[1, 2, 3, 4], [5, 6, 7, 8], [9, 10, 11, 12], [13, 14, 15, 0]]

def generate_board_matrix(numbers, game_board):
    numbers_in_game_board = game_board.split(' ')
    matrix = []
    arr = []

    for i in range(0, len(numbers_in_game_board)):
        arr.append(int(numbers_in_game_board[i]))

        if numbers == 3:
            if (i + 1) % 2 == 0:
                matrix.append(arr)
                arr = []
        elif numbers == 8:                
            if (i + 1) % 3 == 0:
                matrix.append(arr)
                arr = []
        elif numbers == 15:
            if (i + 1) % 4 == 0:
                matrix.append(arr)
                arr = []
        else:
            print('Error!!')

    return matrix

class SlidingBlocks:
    def __init__(self, numbers, board=[], num_moves=-1):
        self.numbers = numbers
        self.num_moves = num_moves + 1
        self.board = board
        self.path = ''
        self.zero_x, self.zero_y = self.get_indexes(self.board, 0)
        self.function = self.num_moves + self.calculate_manhattan_road(board)
        self.children_objects = Q.PriorityQueue()

    def __eq__(self, other):
        return self.function == other.function

    def __lt__(self, other):
        return self.function < other.function

    def __le__(self, other):
        return self.function <= other.function

    def __gt__(self, other):
        return self.function > other.function

    def __ge__(self, other):
        return self.function >= other.function

    def __ne__(self, other):
        return self.function != other.function

    def __str__(self):
        return self.print_board()
    
    def __repr__(self):
        return self.print_board()

    def get_indexes(self, matrix, element):
        for row in matrix:
            for col in matrix:
                x = matrix.index(row)
                y = matrix.index(col)
                if matrix[x][y] == element:
                    return (x, y)

    def calculate_manhattan_road(self, matrix):
        manhattan_sum = []
        for row in matrix:
            for col in matrix:
                x = matrix.index(row)
                y = matrix.index(col)
                el = matrix[x][y]

                if (el != 0):
                    final_el_x, final_el_y = self.get_indexes(final_board_8, el)
                    # Sum = (|Xi - Xj| + |Yi - Yj|)
                    X = abs(final_el_x - x)
                    Y = abs(final_el_y - y)

                    f = int(X) + int(Y) 
                    manhattan_sum.append(f)
        
        return sum(manhattan_sum)

    def move_element(self, new_zero_x, new_zero_y):
        '''
        Подавайки на функцията новите координати на 0,
        тя прави копие на секашната матрица, взима елемента, който трябва да преместим и разменя 0 и елемента;
        Създава нов обект от това дете със същия брой елементи, новата матрица и увеличава броя преместени стъпки.

        Функцията връща новата матрица и новия обект, който после се добавя в приоритетната опашка
        '''
        matrix = copy.deepcopy(self.board)
        element = matrix[new_zero_x][new_zero_y]

        matrix[self.zero_x][self.zero_y] = element
        matrix[new_zero_x][new_zero_y] = 0

        new_child = SlidingBlocks(self.numbers, board=matrix, num_moves=self.num_moves)

        return new_child

    def generate_children(self):
        '''
        Генерирам всички деца на текушия руут, като всяко дете го правя нов обект, 
        подавайки му новата матрица с преместен елемент и увеличавам броя на преместванията, 
        които се добавят към оценяващата функция

        Всички тези новосъздадени обекти-деца ги добавям към приоритетната опашка на текущич елемент
        и така винаги като гетвам, ще гетвам най-доброто дете на родителя

        '''
        if (self.zero_y - 1 >= 0):
            ch_l = self.move_element(self.zero_x, self.zero_y - 1)
            ch_l.path = 'right'
            self.children_objects.put((ch_l.function, ch_l))  

        if (self.zero_y + 1 <= len(self.board) - 1):
            ch_r = self.move_element(self.zero_x, self.zero_y + 1)
            ch_r.path = 'left'
            self.children_objects.put((ch_r.function, ch_r))

        if (self.zero_x - 1 >= 0):
            ch_u = self.move_element(self.zero_x - 1, self.zero_y)
            ch_u.path = 'down'
            self.children_objects.put((ch_u.function, ch_u))

        if (self.zero_x + 1 <= len(self.board) - 1):
            ch_d = self.move_element(self.zero_x + 1, self.zero_y)
            ch_d.path = 'up'
            self.children_objects.put((ch_d.function, ch_d))

        return self.children_objects


    def is_final_state(self):
        if self.numbers == 3:
            return self.board == final_board_3 
        elif self.numbers == 8:
            return self.board == final_board_8
        elif self.numbers == 15:
            return self.board == final_board_15

    def print_board(self):
        b = ""
        for row in self.board:
            for cell in row:
                b += str(cell) + ' '
            b += '\n'
        return b

def rec_a_star(node, visited=[], path=''):
    visited.append(node.board)
    children = node.generate_children()
    
    while not children.empty():
        current_child = children.get()

        if current_child[1].is_final_state():
            path += '\n' + current_child[1].path
            print(path)
            return True

        if current_child[1].board not in visited:
            path += '\n' + current_child[1].path
            # ако е намерило решение да се върне нагоре по рекурсията
            if rec_a_star(current_child[1], visited, path):
                return True
    
    return False
            
def main():
    numbers = int(input("Enter N (3, 8, 15):"))
    if numbers not in [3, 8, 15]:
        numbers = int(input("Invalid value. Enter new N (3, 8, 15):"))
    
    game_board = input("Enter board with spaces\n")
    board = generate_board_matrix(numbers, game_board)

    sb = SlidingBlocks(numbers, board=board)
    print(sb)
    start = rec_a_star(sb)


if __name__ == '__main__':
    main()

# examples
# 1 2 3 0 4 5 7 8 6 
# 1 2 3 4 5 6 0 7 8
# 1 2 3 8 0 5 4 7 6
# 7 2 4 0 5 6 8 3 1