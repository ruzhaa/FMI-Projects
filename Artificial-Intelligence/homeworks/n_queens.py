import random
from copy import copy
import datetime

def calculate_conflicts(list_with_queens):
    queens_conflicts = []
    for q in range(len(list_with_queens)):
        conflicts = 0
        ROW_queen = q
        COL_queen = list_with_queens[q]

        for next_queen in range(len(list_with_queens)):
            
            ROW_next_queen = next_queen
            COL_next_queen = list_with_queens[next_queen]

            # преспята конфликтите по диагонали
            if q != next_queen and (abs(ROW_queen - ROW_next_queen) == abs(COL_queen - COL_next_queen)):
                conflicts += 1
            
            # преспята конфликтите по редове
            if q != next_queen and COL_queen == COL_next_queen:
                conflicts += 1
        
        queens_conflicts.append(conflicts)
    return queens_conflicts

def has_conflicts(queens_conflicts):
    return sum([q for q in queens_conflicts ]) != 0

'''
    връща масив с индексите, където има маск конфликти и връща на рандом
'''
def get_queen_with_max_conflicts(list_with_queens):
    list_with_conflicts = calculate_conflicts(list_with_queens)
    max_conflicts = max(list_with_conflicts)

    idxs_with_max_conflicts = [i for i, j in enumerate(list_with_conflicts) if j == max_conflicts]

    idx = random.choice(idxs_with_max_conflicts)
    # връща реда и колоната на царицата с максимум конфликта
    # print('row', list_with_queens[idx], 'col', idx)
    return idx

'''
    започва последователно да мести царицата по колоната нагоре надолу
    и пресмята за всяка нова позициия конфликтите, които ще се получат и ги пълни в масива
    list_with_conflicts_for_max_queen, който накрая се връща
'''
def get_list_with_conflicts_with_new_position_queens(list_with_queens, col):
    curren_list = copy(list_with_queens)
    list_with_conflicts_for_max_queen = []

    for new_position in range(len(list_with_queens)):
        curren_list[col] = new_position
        list_with_conflicts_for_max_queen.append(calculate_conflicts(curren_list)[col])
    
    return list_with_conflicts_for_max_queen
'''
    търси миналните конфликти, взима индекса на масива,
    в който е и следователно това е новата позиция (реда в колоната), на която 
    трябва да се премести царицата
'''
def get_list_with_queens_with_min_conflicts(list_with_queens, col):
    list_with_conflicts = get_list_with_conflicts_with_new_position_queens(list_with_queens, col)
    min_conflicts = min(list_with_conflicts)

    idxs_with_min_conflicts = [i for i, j in enumerate(list_with_conflicts) if j == min_conflicts]
    idx = random.choice(idxs_with_min_conflicts)
    
    return idx

def n_queens(number_queens):
    MAX_ITER = int(number_queens)*2
    iteration = 0

    list_with_queens = [x for x in range(int(number_queens))]
    random.shuffle(list_with_queens)

    while iteration < MAX_ITER:
        if has_conflicts(calculate_conflicts(list_with_queens)):
            col = get_queen_with_max_conflicts(list_with_queens)
            new_col = get_list_with_queens_with_min_conflicts(list_with_queens, col)
            list_with_queens[col] = new_col
        
        iteration += 1
    
    if has_conflicts(calculate_conflicts(list_with_queens)):
        n_queens(number_queens)
    else:
        print_grid(list_with_queens, number_queens)

def print_grid(list_with_queens, number_queens):
    file_name = 'result_' + number_queens + '.txt'
    
    # with open(file_name, 'a') as f:
    for i in range(int(number_queens)):
        row = ['-'] * int(number_queens)
        for col in range(int(number_queens)):
            if list_with_queens[col] == i:
                row[col] = 'Q'
        line = ''.join(row)
        print(line)
        # f.write("{}\n".format(line))


if __name__ == '__main__':
    number_queens = input("Enter number of queens: ")

    file_name = 'result_' + number_queens + '.txt'
    # with open(file_name, 'w') as f:
    #     f.write("Number of queens: {}\n".format(number_queens))
    #     f.write("\n\nALGORITH RESULTS\n\n")

    start_time = datetime.datetime.now()
    n_queens(number_queens)
    
    end_time = datetime.datetime.now()
    time = (end_time - start_time).total_seconds() 
    print(time)
    # with open(file_name, 'a') as f:
        # f.write("TIME: {}\n".format(time))
    
    print("Open `{}` to check the results!".format(file_name))
