#!/usr/bin/env python3

"""Agricultural Data Logger using Tkinter.

This simple application lets the user record information about crop
activities. Each entry is saved to a CSV file. The UI displays all
stored entries in a table.
"""

import csv
import os
from datetime import date
import tkinter as tk
from tkinter import ttk, messagebox

DATA_FILE = 'agri_data.csv'


class AgriLogger(tk.Tk):
    def __init__(self):
        super().__init__()
        self.title("Agricultural Data Logger")

        self._create_widgets()
        self._load_data()

    def _create_widgets(self):
        self.date_var = tk.StringVar(value=date.today().isoformat())
        self.crop_var = tk.StringVar()
        self.amount_var = tk.StringVar()
        self.notes_var = tk.StringVar()

        row = 0
        tk.Label(self, text="Date (YYYY-MM-DD):").grid(row=row, column=0, sticky="e")
        tk.Entry(self, textvariable=self.date_var).grid(row=row, column=1, sticky="w")
        row += 1

        tk.Label(self, text="Crop:").grid(row=row, column=0, sticky="e")
        tk.Entry(self, textvariable=self.crop_var).grid(row=row, column=1, sticky="w")
        row += 1

        tk.Label(self, text="Amount:").grid(row=row, column=0, sticky="e")
        tk.Entry(self, textvariable=self.amount_var).grid(row=row, column=1, sticky="w")
        row += 1

        tk.Label(self, text="Notes:").grid(row=row, column=0, sticky="ne")
        tk.Entry(self, textvariable=self.notes_var, width=40).grid(row=row, column=1, sticky="w")
        row += 1

        tk.Button(self, text="Save Entry", command=self._save_entry).grid(row=row, column=0, columnspan=2, pady=5)
        row += 1

        self.tree = ttk.Treeview(self, columns=("date", "crop", "amount", "notes"), show='headings')
        self.tree.heading("date", text="Date")
        self.tree.heading("crop", text="Crop")
        self.tree.heading("amount", text="Amount")
        self.tree.heading("notes", text="Notes")
        self.tree.grid(row=row, column=0, columnspan=2, sticky="nsew")

        self.grid_rowconfigure(row, weight=1)
        self.grid_columnconfigure(1, weight=1)

    def _load_data(self):
        self.tree.delete(*self.tree.get_children())
        if os.path.exists(DATA_FILE):
            with open(DATA_FILE, newline='', encoding='utf-8') as csvfile:
                reader = csv.DictReader(csvfile)
                for row in reader:
                    self.tree.insert('', 'end', values=(row['date'], row['crop'], row['amount'], row['notes']))

    def _save_entry(self):
        entry = {
            "date": self.date_var.get(),
            "crop": self.crop_var.get(),
            "amount": self.amount_var.get(),
            "notes": self.notes_var.get(),
        }

        file_exists = os.path.exists(DATA_FILE)
        with open(DATA_FILE, 'a', newline='', encoding='utf-8') as csvfile:
            fieldnames = ["date", "crop", "amount", "notes"]
            writer = csv.DictWriter(csvfile, fieldnames=fieldnames)
            if not file_exists:
                writer.writeheader()
            writer.writerow(entry)

        self.tree.insert('', 'end', values=(entry["date"], entry["crop"], entry["amount"], entry["notes"]))
        self.crop_var.set("")
        self.amount_var.set("")
        self.notes_var.set("")
        messagebox.showinfo("Saved", "Entry saved successfully.")


def main():
    app = AgriLogger()
    app.mainloop()


if __name__ == "__main__":
    main()
