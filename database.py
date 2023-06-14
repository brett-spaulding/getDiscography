import operator as oprtr
from pysondb import PysonDB


def evaluate_condition(record_field, operator, condition):
    return operator(record_field, condition)


def evaluate_operator(op):
    if op == '>':
        op = oprtr.gt
    elif op == '<':
        op = oprtr.lt
    elif op == '=':
        op = oprtr.eq
    elif op == '!=':
        op = oprtr.ne
    else:
        raise UserWarning('Invalid Operator: %s' % op)
    return op


class Model:
    # TODO: Modify some of this to be wrapped into an ENV wrapper that gets loaded in when the server starts and creates
    #  class objects that can be manipulated easier by things like update_by_id

    def __init__(self, name):
        self.env = PysonDB('/home/stonesoft/Apps/getDiscography/database/%s.json' % name)

    def _search(self, records, params):
        """
        Iterate through list of condition tuples and append results to a checklist that will evaluate at the end
        ex params: [('name', '=', 'John'), ('zip', '!=', '12345')]
        :param params: List of tuples
        :return: Record to search recordset if True
        """
        filtered_record_ids =[]
        for record in records:
            record_id = self.env.get_by_id(record)
            print('===')
            print(record_id)
            checklist = []
            for param in params:
                field = param[0]
                operator = evaluate_operator(param[1])
                condition = param[2]
                checklist.append(evaluate_condition(record_id[field], operator, condition))

            passed = all(x for x in checklist)
            if passed:
                filtered_record_ids.append(record_id)

        return filtered_record_ids

    def search(self, params):
        """
        :param params: List of tuples that will be evaluated and return a total list of records
        :return: None, List or Single record
        """
        records = self.env.get_all()
        record_ids = self._search(records, params)
        if not record_ids:
            record_ids = None

        return record_ids

    def read(self, record_id):
        data = self.env.get_by_id(record_id)
        return data

    def create(self, vals):
        record = self.env.add(vals)
        return record

    def create_many(self, record_list):
        record_ids = self.env.add_many(record_list)
        return record_ids

    def write(self, record_id, vals):
        self.env.update_by_id(record_id, vals)

    def unlink(self, record_id):
        self.env.delete_by_id(record_id)
        return True
