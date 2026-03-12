import pandas as pd
import json
import sys

def parse_excel(file_path):
    xls = pd.ExcelFile(file_path)
    # We use 'Budget Summary' as it has the rollup structure
    df = pd.read_excel(xls, 'Budget Summary', header=5)
    
    # Filter out empty descriptions
    df = df[df.iloc[:, 2].notna()]
    
    items = []
    parent_map = {} # Maps code prefix to ID
    
    # Columns: 0:?, 1:?, 2:Code, 3:Description, 4:Total Amount
    # Adjusting based on previous inspection (iloc references are safer)
    
    for _, row in df.iterrows():
        code = str(row.iloc[2]).strip() if pd.notna(row.iloc[2]) else ''
        desc = str(row.iloc[3]).strip() if pd.notna(row.iloc[3]) else ''
        
        try:
            val = row.iloc[4]
            amount = float(val) if pd.notna(val) and not isinstance(val, str) else 0.0
            if isinstance(val, str):
                # If it's a string that looks like a number, try to convert
                try: amount = float(val.replace(',', '').replace('$', ''))
                except: amount = 0.0
        except:
            amount = 0.0
        
        if not code or code == 'nan' or not desc or desc == 'nan':
            continue
            
        # Skip literal header rows
        if code.lower() == 'code' or desc.lower() == 'description':
            continue
        # CSI Code Parsing (assuming XX XX XX format)
        is_section = 0
        parent_code = None
        csi_div = None
        
        parts = code.split()
        if len(parts) >= 1:
            csi_div = parts[0] # Division (e.g., 03)
            
        if len(parts) == 3:
            if parts[1] == '00' and parts[2] == '00':
                is_section = 1
                parent_code = None
            elif parts[2] == '00':
                is_section = 1
                parent_code = parts[0] + " 00 00"
            else:
                is_section = 0
                parent_code = parts[0] + " " + parts[1] + " 00"
        
        # If amount is 0 and it looks like a header (all caps or trailing zeroes), force is_section
        if amount == 0 and not is_section:
             if desc.isupper() or len(parts) < 3:
                is_section = 1
             
        items.append({
            'item_code': code,
            'description': desc,
            'total_amount': amount,
            'is_section': is_section,
            'parent_code': parent_code,
            'csi_division': csi_div,
            'quantity': 1 if not is_section else 0,
            'unit_rate': amount if not is_section else 0,
            'unit': 'LS' if not is_section else ''
        })
        
    return items

if __name__ == "__main__":
    file_arg = sys.argv[1] if len(sys.argv) > 1 else 'c:\\wamp64\\www\\staging\\old_files\\Estimate_Master_SHARE_v1.xlsx'
    try:
        data = parse_excel(file_arg)
        print(json.dumps(data))
    except Exception as e:
        print(json.dumps({"error": str(e)}))
